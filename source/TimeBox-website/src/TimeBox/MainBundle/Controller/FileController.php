<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

use TimeBox\MainBundle\Entity\File;
use TimeBox\MainBundle\Entity\Version;

class FileController extends Controller
{
    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function showAction($folderId)
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $files = $em->getRepository('TimeBoxMainBundle:File')->getRootFiles($user, $folderId);
        $folders = $em->getRepository('TimeBoxMainBundle:Folder')->findByParent($folderId);

        $breadcrumb = array();
        if (!is_null($folderId)) {
            $currentFolder = $em->getRepository('TimeBoxMainBundle:Folder')->find($folderId);
            if (!is_null($currentFolder)) {
                $breadcrumb[] = $currentFolder;
                $parent = $currentFolder->getParent();
                while (!is_null($parent)) {
                    $breadcrumb[] = $parent;
                    $parent = $parent->getParent();
                }
                $breadcrumb = array_reverse($breadcrumb);
            }
        }

        $types = array(
            "avi", "bmp", "css", "doc", "gif", "htm", "jpg", "js", "mov", "mp3", "mp4",
            "mpg", "pdf", "php", "png", "ppt", "rar", "txt", "xls", "xml", "zip"
        );

        return $this->render('TimeBoxMainBundle:File:show.html.twig', array(
            "types"   => $types,
            "files"   => $files,
            "folders" => $folders,
            "breadcrumb" => $breadcrumb
        ));
    }

    /**
     * @Template()
     */
    public function uploadAction(Request $request)
    {
        $user = $this->getConnectedUser();

        $file = new File();
        $form = $this->createFormBuilder($file)
            ->add('file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file->setUser($user);
            $file->setUploadName();
            $file->setUploadType();
            $size = $file->getUploadSize();


            $version = new Version();
            $version->setDate(new \DateTime);
            $version->setFile($file);
            $version->setSize($size);
            $version->setDisplayId(0);

            $em->persist($file);
            $em->persist($version);
            $em->flush();

            return $this->redirect($this->generateUrl('time_box_main_file'));
        }

        return array('form' => $form->createView());
    }
}
