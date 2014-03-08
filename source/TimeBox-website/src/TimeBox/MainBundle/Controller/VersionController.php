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

    public function restoreAction()
    {


        $user = $this->getConnectedUser();
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $versionId = json_decode($request->request->get('versionId'));

        
            $versionRepository = $em->getRepository('TimeBoxMainBundle:Version');

            if(is_null($versionRepository))
                throw $this->createNotFoundException('Unable to find version repository.');

            if(is_null($versionId))
                throw $this->createNotFoundException('POST request corrupted.');

            $previousVersion = $versionRepository->findOneBy(
                array('id' => $versionId)
                );

            if(is_null($previousVersion))
                throw $this->createNotFoundException("Unable to find previous version entity.".$versionId);

            $previousVersionFile = $previousVersion->getFile();


            $lastVersion = $versionRepository->findOneBy(
                    array('file' => $previousVersionFile),
                    array('displayId' => 'DESC')
                );
            
            if(is_null($lastVersion))
                throw $this->createNotFoundException('Unable to find Version entity.');

            $versionDisplayId = $lastVersion->getDisplayId() + 1;
            $size = $previousVersion->getSize();



            $restoredVersion = new Version();
            $restoredVersion->setFile($previousVersionFile);
            $restoredVersion->setDisplayId($versionDisplayId);
            $restoredVersion->setSize($size);
            $restoredVersion->setDate(new \DateTime);
            $restoredVersion->setDescription("Restored file.");
            $restoredVersion->setComment($previousVersion->getComment());

            $user->setStorage(max($user->getStorage() + $size, 0));

            $previousVersionFile->setTotalSize($previousVersionFile->getTotalSize()+$size);
            $em->persist($restoredVersion);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('time_box_main_file', array(
                    'folderId' => $previousVersionFile->getFolder()
                )));
        
        }

        return new Response('');
    }

}
