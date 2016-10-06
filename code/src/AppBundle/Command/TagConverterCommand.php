<?php

namespace AppBundle\Command;

use AppBundle\Entity\EvernoteUser;
use Doctrine\ORM\EntityManagerInterface;
use EDAM\NoteStore\NoteFilter;
use EDAM\NoteStore\NoteList;
use EDAM\NoteStore\NotesMetadataResultSpec;
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
        $users = $this->em->getRepository(EvernoteUser::class)->findAll();

        $output->writeln(sprintf("<info>found</info> %s users", count($users)));

        foreach ($users as $user) {
            $client = new Client($user->getToken(), true);

            $nFilter = new NoteFilter();
            $nFilter->words = "reminderTime:* -reminderDoneTime:*";

            $rSpec = new NotesMetadataResultSpec();
            $rSpec->includeTitle = true;
            $rSpec->includeAttributes = true;

            /** @var NoteList $notesData */
            $notesData = $client->getUserNotestore()->findNotesMetadata($client->getToken(), $nFilter, 0, 50, $rSpec);

            foreach ($notesData->notes as $note) {
                $output->writeln($note->title);
            }
        }
    }
}
