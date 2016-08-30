<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
use EDAM\NoteStore\NoteFilter;
use EDAM\NoteStore\NoteList;
use EDAM\NoteStore\NoteMetadata;
use EDAM\NoteStore\NotesMetadataResultSpec;
use Evernote\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NoteController
 * @package AppBundle\Controller
 */
class NoteController extends Controller
{
    /**
     * @Route("/list", name="note_list")
     *
     * @return Response
     */
    public function listAction() : Response
    {
        $notes = $this->getNotes();

        // replace this example code with whatever you need
        return $this->render(':note:list.html.twig', ["notes" => $notes]);
    }

    /**
     * @return Note[]
     */
    private function getNotes() : array
    {
        $token = $this->get("session")->get("en_token");

        $client = new Client($token, true);

        $nFilter = new NoteFilter();
        $nFilter->words = "reminderTime:* -reminderDoneTime:*";

        $rSpec = new NotesMetadataResultSpec();
        $rSpec->includeTitle = true;
        $rSpec->includeAttributes = true;

        /** @var NoteList $notesData */
        $notesData = $client->getUserNotestore()->findNotesMetadata($client->getToken(), $nFilter, 0, 50, $rSpec);

        $sortedNotes = $this->sortNotes($notesData->notes);
        $notes = [];
        /** @var NoteMetadata $noteData */
        foreach ($sortedNotes as $noteData) {
            $note = new Note();
            $note
                ->setId($noteData->guid)
                ->setTitle($noteData->title)
                ->setContent("unavailable")
                ->setCreatedAt((new \DateTime())->setTimestamp($noteData->created))
                ->setRemindAt((new \DateTime())->setTimestamp($noteData->attributes->reminderTime / 1000));

            $notes[] = $note;
        }

        return $notes;
    }

    /**
     * @param NoteMetadata[] $notes
     *
     * @return mixed
     */
    private function sortNotes(array $notes) : array
    {
        usort($notes, function($a, $b) {
            if ($a->attributes->reminderTime == $b->attributes->reminderTime) {
                return 0;
            }

            return $a->attributes->reminderTime > $b->attributes->reminderTime ? 1 : -1;
        });

        return $notes;
    }
}
