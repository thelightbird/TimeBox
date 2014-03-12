<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function newLinkFileAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $filesId = $request->request->get('filesId');
            $filesId = json_decode($filesId);


            $files = $em->getRepository('TimeBoxMainBundle:File')->findBy(array(
                'user' => $user,
                'id' => $filesId
            ));

            if (!is_null($files) && sizeof($files) > 0) {
                foreach ($files as $file) {
                    $existingLinks = $em->getRepository('TimeBoxMainBundle:Link')->findBy(array(
                        'user' => $user,
                        'file' => $file
                        ));

                    if(sizeof($existingLinks) == 0){
                        $link = new Link();
                        $link->setUser($user);
                        $link->setFile($file);
                        $link->setDate(new \DateTime);
                        $link->setDownloadHash("hash"); // TODO downloadHash

                        $em->persist($link);
                    }
                }
                $em->flush();
            }



            $response = 'File';
            if(sizeof($files) != 1)
                $response += 's';
            $response += ' shared!';

            return new Response($response);
        }
        return $this->renderText("Something went wrong");
    }

     public function newLinkVersionAction($versionId)
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $version = $em->getRepository('TimeBoxMainBundle:Version')->findOneById($version);
        if (!$version) {
            throw $this->createNotFoundException('Unable to find Version entity.');
        }

        $link = new Link();
        $link->setUser($user);
        $link->setVersion($version);
        $link->setDownloadHash("hash"); // TODO downloadHash

        $em->persist($link);
        $em->flush();

        return $this->redirect($this->generateUrl('time_box_main_share'));
    }
}