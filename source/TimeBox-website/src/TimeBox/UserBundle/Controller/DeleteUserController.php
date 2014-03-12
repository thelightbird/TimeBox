<?php
namespace TimeBox\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use TimeBox\UserBundle\Entity\User;

class DeleteUserController extends Controller
{

    public function getConnectedUser()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        return $user;
    }

    public function deleteAction(Request $request)
    {
        
        $user = $this->getConnectedUser();

        $em = $this->getDoctrine()->getManager();

        $links = $em->getRepository("TimeBoxMainBundle:Link")->findByUser($user);
    
        foreach ($links as $link) {
            $em->remove($link);
        }

        $files = $em->getRepository("TimeBoxMainBundle:File")->findByUser($user);

        foreach ($files as $file) {
            $versions = $em->getRepository("TimeBoxMainBundle:Version")->findByFile($file);
            foreach ($versions as $version) {
                $em->remove($version);
            }
            $em->remove($file);
        }

        $folders = $em->getRepository("TimeBoxMainBundle:Folder")->findByUser($user);

        foreach ($folders as $folder) {
            $em->remove($folder);
        }

        $em->remove($user);
        $em->flush();


        return $this->render('TimeBoxUserBundle:DeleteUser:deleteUser.html.twig');
    }
}