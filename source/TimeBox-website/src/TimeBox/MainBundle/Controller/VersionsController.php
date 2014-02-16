<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use TimeBox\MainBundle\Entity\File;
use TimeBox\MainBundle\Entity\Version;

class VersionsController extends Controller
{

    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function indexAction($fileId)
    {

        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $versions = $em->getRepository('TimeBoxMainBundle:Version')->getVersionsOfFile($fileId, $user);

        $types = array(
            "avi", "bmp", "css", "doc", "gif", "htm", "jpg", "js", "mov", "mp3", "mp4",
            "mpg", "pdf", "php", "png", "ppt", "rar", "txt", "xls", "xml", "zip"
        );

        return $this->render('TimeBoxMainBundle:Versions:index.html.twig', array(
            "versions" => $versions
        ));
    }

    
}
