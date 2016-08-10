<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Note;
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
        $note1 = new Note();
        $note1
            ->setId(1)
            ->setTitle("first note")
            ->setContent("first note content")
            ->setRemindAt(new \DateTime("+1 hour"))
            ->setCreatedAt(new \DateTime());

        $note2 = new Note();
        $note2
            ->setId(2)
            ->setTitle("second note")
            ->setContent("second note content")
            ->setRemindAt(new \DateTime("+1 day"))
            ->setCreatedAt(new \DateTime());

        $note3 = new Note();
        $note3
            ->setId(3)
            ->setTitle("third note")
            ->setContent("third note content")
            ->setRemindAt(new \DateTime("+2 days 12:00:00"))
            ->setCreatedAt(new \DateTime());

        return [$note1, $note2, $note3];
    }
}
