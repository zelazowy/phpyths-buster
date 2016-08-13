<?php

namespace AppBundle\Controller;

use Evernote\Auth\OauthHandler;
use Evernote\Exception\AuthorizationDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EvernoteController
 * @package AppBundle\Controller
 */
class EvernoteController extends Controller
{
    /**
     * @Route("/connect", name="evernote_connect")
     *
     * @return RedirectResponse
     * @throws AuthorizationDeniedException
     */
    public function connectAction()
    {
        $key = $this->getParameter("consumer_key");
        $secret = $this->getParameter("consumer_secret");

        $oauthHandler = new OauthHandler(true, false, false);
        $tokenData = $oauthHandler->authorize($key, $secret, $this->generateUrl("evernote_connect", [], UrlGeneratorInterface::ABSOLUTE_URL));

        if (false === empty($tokenData)) {
            $token = $tokenData["oauth_token"];
            $this->get("session")->set("en_token", $token);

            return $this->redirectToRoute("note_list");
        }

        return new Response();
    }
}
