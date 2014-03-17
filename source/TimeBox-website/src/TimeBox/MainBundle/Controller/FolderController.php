<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use TimeBox\MainBundle\Entity\Folder;

class FolderController extends Controller
{

    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function newAction()
    {
        $user = $this->getConnectedUser();
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $parentFolderId = $request->query->get('parentFolderId');

        if ($request->getMethod() == 'POST') {
            $folderName = $request->request->get('folderName');
            $folderParent = $request->request->get('folderParent');

            $folder = new Folder();
            $folder->setName($folderName);
            $folder->setUser($user);
            if (is_numeric($folderParent)) {
                $parent = $em->getRepository('TimeBoxMainBundle:Folder')->findOneBy(array(
                    'id'   => $folderParent,
                    'user' => $user
                ));
                $folder->setParent($parent);
            }

            $em->persist($folder);
            $em->flush();

            return $this->redirect($this->generateUrl('time_box_main_file', array(
                'folderId' => $folderParent
            )));
        }

        return $this->render('TimeBoxMainBundle:Folder:new.html.twig', array(
            "parentFolderId" => $parentFolderId
        ));
    }
}
