<?php

namespace AppBundle\Command;

use AppBundle\Entity\EvernoteUser;
use AppBundle\Helper\SpecialCharactersPurifier;
use Doctrine\ORM\EntityManagerInterface;
use EDAM\NoteStore\NoteFilter;
use EDAM\NoteStore\NoteList;
use EDAM\NoteStore\NotesMetadataResultSpec;
use EDAM\Types\Tag;
use Evernote\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TagConverterCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName("app:tag_converter")
            ->setDescription("Converts tags placed in note's title in format #tag to Evernote native tags");
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->em = $this->getContainer()->get("doctrine.orm.default_entity_manager");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->em->getRepository(EvernoteUser::class)->findActive();

        $output->writeln(sprintf("<info>found</info> %s users", count($users)));

        foreach ($users as $user) {
            $client = new Client($user->getToken(), true);

            $tagsRaw = $client->getUserNotestore()->listTags($client->getToken());
            $tags = [];
            /** @var Tag $tag */
            foreach ($tagsRaw as $tag) {
                // remove special characters from tag name
                $tags[$tag->guid] = SpecialCharactersPurifier::purify($tag->name);
            }

            $output->writeln(var_export($tags, true), OutputInterface::VERBOSITY_DEBUG);

            $nFilter = new NoteFilter();
            $nFilter->words = "intitle:_*";

            $rSpec = new NotesMetadataResultSpec();
            $rSpec->includeTitle = true;
            $rSpec->includeAttributes = true;

            /** @var NoteList $notesData */
            $notesData = $client->getUserNotestore()->findNotesMetadata($client->getToken(), $nFilter, 0, 50, $rSpec);

            foreach ($notesData->notes as $noteData) {
                /** @var \EDAM\Types\Note $note */
                $note = $client->getUserNotestore()->getNote($client->getToken(), $noteData->guid, true, true, true, true);

                $output->writeln(sprintf("<info>%s</info>", $note->title));

                // remove any latin character from title when it appears after `_` sign
                $title = preg_replace_callback(
                    "/_(.+)\b/iu", // \b is whitespace or end of line, `u` modifier is requred for searching for unicode chars
                    function ($matches) {
                        return SpecialCharactersPurifier::purify($matches[0]);
                    },
                    $note->title
                );

                $output->write("purified title: {$title} ", OutputInterface::VERBOSITY_VERBOSE);

                // find tags
                $tagsFound = false;
                foreach ($tags as $tid => $tag) {
                    $output->write("searching for _{$tag} ", OutputInterface::VERBOSITY_VERBOSE);

                    $title = preg_replace_callback(
                        "/_{$tag}/i",
                        function ($matches) use ($note, $tag, $tid, &$tagsFound, $output) {
                            $output->write("found {$matches[0]}", OutputInterface::VERBOSITY_VERBOSE);

                            $note->tagGuids[] = $tid;
                            $tagsFound = true;

                            return "";
                        },
                        $title
                    );

                    $output->writeln("", OutputInterface::VERBOSITY_VERBOSE);
                }

                // update note only when it contains tags in title
                if ($tagsFound) {
                    $output->writeln(sprintf("new title: <info>%s</info>", $title), OutputInterface::VERBOSITY_VERBOSE);

                    $note->title = trim($title);
                    $client->getUserNotestore()->updateNote($client->getToken(), $note);
                }
            }
        }
    }
}
