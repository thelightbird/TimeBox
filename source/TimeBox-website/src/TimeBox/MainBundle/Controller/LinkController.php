<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use TimeBox\MainBundle\Entity\Link;

class LinkController extends Controller
{
    /**
     * Get current logged user.
     *
     */
    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }


    /**
     * Show shared links for current logged user.
     *
     */
    public function showAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $links = $em->getRepository('TimeBoxMainBundle:Link')->findByUser($user,
            array(
                "date" => "ASC"
            ));

        // types that have an icon
        $types = array(
            "avi", "bmp", "css", "doc", "gif", "htm", "jpg", "js", "mov", "mp3", "mp4",
            "mpg", "pdf", "php", "png", "ppt", "rar", "txt", "xls", "xml", "zip"
        );

        return $this->render('TimeBoxMainBundle:Link:show.html.twig', array(
            "links" => $links,
            "types" => $types
        ));
    }


    /**
     * Create a new shared link for lastest version of a file.
     *
     */
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
                        $time = time();
                        $hash = md5($time + $user->getId()) . sha1($time + $user->getUsername());
                        $link->setDownloadHash($hash);

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


    /**
     * Create a new shared link for a specific version of a file.
     *
     */
    public function newLinkVersionAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $versionId = $request->request->get('versionId');
            $versionId = json_decode($versionId);

            $version = $em->getRepository('TimeBoxMainBundle:Version')->findBy(array(
                'id' => $versionId
            ));

            if (!is_null($version)) {
                $existingLinks = $em->getRepository('TimeBoxMainBundle:Link')->findBy(array(
                    'user' => $user,
                    'version' => $version
                    ));

                if(sizeof($existingLinks) == 0){
                    $link = new Link();
                    $link->setUser($user);
                    $link->setVersion($version[0]);
                    $link->setDate(new \DateTime);
                    $time = time();
                    $hash = md5($time + $user->getId()) . sha1($time + $user->getUsername());
                    $link->setDownloadHash($hash);

                    $em->persist($link);
                }
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('time_box_main_share'));
    }


    /**
     * Delete one or more shared links.
     *
     */
    public function deleteAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $linksId = $request->request->get('linksId');
            $linksId = json_decode($linksId);

            $links = $em->getRepository('TimeBoxMainBundle:Link')->findBy(array(
                'user' => $user,
                'id' => $linksId
                ));

            foreach ($links as $link) {
                $link->setFile(null);   //to prevent SQL cascading errors
                $link->setVersion(null);
                $link->setUser(null);
                $em->remove($link);
            }

            $em->flush();
        }

        return new Response('');
    }
}
