<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use TimeBox\MainBundle\Entity\Link;

class LinkController extends Controller
{
    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function showAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $links = $em->getRepository('TimeBoxMainBundle:Link')->findByUser($user,
            array(
                "date" => "ASC"
            ));

        $types = array(
            "avi", "bmp", "css", "doc", "gif", "htm", "jpg", "js", "mov", "mp3", "mp4",
            "mpg", "pdf", "php", "png", "ppt", "rar", "txt", "xls", "xml", "zip"
        );

        return $this->render('TimeBoxMainBundle:Link:show.html.twig', array(
            "links" => $links,
            "types" => $types
        ));
    }

    public function newLinkFileAction($fileId)
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $file = $em->getRepository('TimeBoxMainBundle:File')->findOneById($fileId);
        if (!$file) {
            throw $this->createNotFoundException('Unable to find File entity.');
        }

        $link = new Link();
        $link->setUser($user);
        $link->setFile($file);
        $link->setDownloadHash("hash"); // TODO downloadHash

        $em->persist($link);
        $em->flush();

        return $this->redirect($this->generateUrl('time_box_main_share'));
    }
}
