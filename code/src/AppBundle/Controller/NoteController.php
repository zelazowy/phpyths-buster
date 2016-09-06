<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\NoteType;
use EDAM\NoteStore\NoteFilter;
use EDAM\NoteStore\NoteList;
use EDAM\NoteStore\NoteMetadata;
use EDAM\NoteStore\NotesMetadataResultSpec;
use Evernote\Client;
use Evernote\Model\Note;
use Evernote\Model\PlainTextNoteContent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NoteController
 * @package AppBundle\Controller
 */
class NoteController extends Controller
{
    /**
     * @Route("/list", name="note_list")
     */
    public function listAction() : Response
    {
        $notes = $this->getNotes();

        $form = $this->createForm(NoteType::class);

        // replace this example code with whatever you need
        return $this->render(':note:list.html.twig', ["notes" => $notes, "noteForm" => $form->createView()]);
    }

    /**
     * @Route("/new/", name="new_reminder")
     */
    public function newReminder(Request $request) : Response
    {
        $noteType = $this->createForm(NoteType::class);

        $noteType->handleRequest($request);
        if (true === $noteType->isValid()) {
            $note = new Note();
            $note->setTitle($noteType->get("title")->getData());
            $note->setContent(new PlainTextNoteContent($noteType->get("content")->getData()));
            $note->setReminder((new \DateTime($noteType->get("reminder")->getData()))->getTimestamp());

            $token = $this->get("session")->get("en_token");

            $client = new Client($token, true);

            $client->uploadNote($note);
        }

        return $this->redirectToRoute("note_list");
    }

    /**
     * @Route("/done/", name="note_done")
     */
    public function doneAction(Request $request) : Response
    {
        $token = $this->get("session")->get("en_token");

        $client = new Client($token, true);

        $noteId = $request->request->get("id");
        $csrf = $request->request->get("_token");

        if (false === $this->isCsrfTokenValid($noteId, $csrf)) {
            throw new \InvalidArgumentException("invalid token");
        }

        $note = $client->getNote($noteId);
        $doneNote = clone $note;
        $doneNote->setAsDone();

        $client->replaceNote($note, $doneNote);

        return $this->redirectToRoute("note_list");
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

        return $sortedNotes;
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
