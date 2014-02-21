<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use TimeBox\MainBundle\Entity\File;
use TimeBox\MainBundle\Entity\Folder;
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

        $files = $em->getRepository('TimeBoxMainBundle:File')->getRootFiles($user, $folderId, 0);
        $folders = $em->getRepository('TimeBoxMainBundle:Folder')->findBy(array(
            'parent' => $folderId,
            'isDeleted' => 0
        ));

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
            "files" => $files,
            "types" => $types,
            "user" => $user,
            "folderId" => $folderId,
            "folders" => $folders,
            "breadcrumb" => $breadcrumb
        ));
    }

    public function deleteAction()
    {
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $currentFolderId = null;

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $foldersId = $request->request->get('foldersId');
            $foldersId = json_decode($foldersId);
            $filesId = $request->request->get('filesId');
            $filesId = json_decode($filesId);
            $currentFolderId = $request->request->get('currentFolderId');

            if (!is_null($filesId) && sizeof($filesId)>0) {
                $filesToDelete = $em->getRepository('TimeBoxMainBundle:File')->findBy(array(
                    'id'   => $filesId,
                    'user' => $user
                ));
                foreach ($filesToDelete as $file) {
                    $file->setIsDeleted(true);
                }
                $em->flush($filesToDelete);
            }

            if (!is_null($foldersId) && sizeof($foldersId)>0) {
                $foldersToDelete = $em->getRepository('TimeBoxMainBundle:Folder')->findBy(array(
                    'id'   => $foldersId,
                    'user' => $user
                ));
                foreach ($foldersToDelete as $folder) {
                    $folder->setIsDeleted(true);
                }
                $em->flush($foldersToDelete);
            }
        }

        $url = $this->get('router')->generate('time_box_main_file', array(
            'folderId' => $currentFolderId
        ));
        return new Response($url);
    }

    public function moveAction()
    {
        $user = $this->getConnectedUser();
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $currentFolderId = $request->request->get('currentFolderId');
            $moveFolderId = $request->request->get('moveFolderId');
            $foldersId = $request->request->get('foldersId');
            $foldersId = json_decode($foldersId);

            if (!is_null($moveFolderId)) {
                return $this->redirect($this->generateUrl('time_box_main_file', array(
                    'folderId' => $folderId
                )));
            }

            $foldersId = json_encode($foldersId);

            $folders = $em->getRepository('TimeBoxMainBundle:Folder')->findBy(
                array(
                    'user'=>$user
                ),
                array(
                    'parent' => 'ASC',
                    'name' => 'ASC'
                )
            );
            return $this->render('TimeBoxMainBundle:Folder:show.html.twig', array(
                'folders' => $folders,
                'foldersId' => $foldersId,
                'folderId' => $currentFolderId
            ));
        }
        return new Response('');
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
