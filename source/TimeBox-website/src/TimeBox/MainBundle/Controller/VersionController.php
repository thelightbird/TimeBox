<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use TimeBox\MainBundle\Entity\Version;

class VersionController extends Controller
{

    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function indexAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $fileId = $this->get('request')->request->get('fileId');
        if (!is_numeric($fileId)) {
            return new Response('');
        }
        $versions = $em->getRepository('TimeBoxMainBundle:Version')->getFileVersions($user, $fileId);

        return $this->render('TimeBoxMainBundle:Version:show.html.twig', array(
            "versions" => $versions
        ));
    }
}
