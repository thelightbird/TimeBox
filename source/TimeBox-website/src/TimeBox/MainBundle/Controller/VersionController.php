<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use TimeBox\MainBundle\Entity\Version;

class VersionController extends Controller
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
     * Called by an ajax request, display all versions of a file.
     *
     */
    public function indexAction()
    {
        try {
            $user = $this->getConnectedUser();
        }
        catch(AccessDeniedException $e) {
            return new Response('not logged');
        }

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


    /**
     * Restore a version of a file.
     *
     */
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
            $restoredVersion->setDescription("Restored file from version ".$previousVersion->getDisplayId());
            $restoredVersion->setComment($previousVersion->getComment());

            $user->setStorage(max($user->getStorage() + $size, 0));

            $previousVersionFile->setTotalSize($previousVersionFile->getTotalSize()+$size);
            $em->persist($restoredVersion);
            $em->persist($user);
            $em->flush();

            copy($previousVersion->getAbsolutePath(), $restoredVersion->getAbsolutePath());

            return $this->redirect($this->generateUrl('time_box_main_file', array(
                'folderId' => $previousVersionFile->getFolder()
            )));
        }

        return new Response('');
    }


    /**
     * Download a specific version of a file.
     *
     * @param Version  $versionId  The version id.
     */
    public function downloadAction($versionId)
    {
        $user = $this->getConnectedUser();
        $em = $this->getDoctrine()->getManager();

        $versionRepository = $em->getRepository('TimeBoxMainBundle:Version');

        if(is_null($versionRepository))
                throw $this->createNotFoundException('Unable to find version repository.');

        if(is_null($versionId))
                throw $this->createNotFoundException('POST request corrupted.');

        $version = $versionRepository->findOneBy(
            array('id' => $versionId)
        );

        if(is_null($version))
            throw $this->createNotFoundException("Unable to find version entity.".$versionId);

        $file = $version->getFile();

        if(is_null($file))
            throw $this->createNotFoundException("Unable to find file entity.".$versionId);

        $possessFile = $em->getRepository('TimeBoxMainBundle:File')->findOneBy(array(
            'id'   => $file->getId(),
            'user' => $user
        ));

        if(is_null($possessFile))
            return new Response('<html><body>You are not allowed to download this file</body></html>');

        $filePath = $version->getAbsolutePath();
        $filename = $file->getName();
        $type = $file->getType();

        if(!is_null($type))
            $filename .= '.'.$type;

        if(!file_exists($filePath))
            throw $this->createNotFoundException("File not found");

        // Trigger file download
        $response = new Response();
        $response->headers->set('Content-type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s', $filename));
        $response->setContent(file_get_contents($filePath));
        return $response;
    }
}
