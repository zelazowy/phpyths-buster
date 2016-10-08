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

        $output->writeln(sprintf("found <info>%s</info> users", count($users)));

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

                $output->writeln(sprintf("processing note <info>%s</info>", $note->title));
                $output->writeln("------", OutputInterface::VERBOSITY_VERBOSE);

                $tagsFound = false;
                $words = explode(" ", $note->title);
                for ($i = 0; $i < count($words); $i++) {
                    if (0 !== strpos($words[$i], "_")) {
                        continue;
                    }

                    // remove `_` from the beginning of (potential) tag
                    // local $word variable created to avoid changing orignal word when not necessary
                    $word = substr($words[$i], 1);
                    $word = SpecialCharactersPurifier::purify($word);

                    if (false === ($tid = array_search($word, $tags))) {
                        continue;
                    }

                    $output->writeln(sprintf("found <warning>%s</warning> tag", $word), OutputInterface::VERBOSITY_VERBOSE);

                    $note->tagGuids[] = $tid;
                    unset($words[$i]);
                    $tagsFound = true;
                }

                if ($tagsFound) {
                    $title = implode(" ", $words);
                    $output->writeln(sprintf("new title: <info>%s</info>", $title), OutputInterface::VERBOSITY_VERBOSE);

                    $note->title = trim($title);
                    $client->getUserNotestore()->updateNote($client->getToken(), $note);
                }
            }
        }
    }
}
