<?php
namespace TimeBox\UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use TimeBox\UserBundle\Entity\User;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        $response = parent::registerAction($request);

        $user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user)) {
            $user->setRegistrationIp($request->getClientIp());
            $user->setLastLogin(new \Datetime());
            $user->setLastLoginIp($request->getClientIp());

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updateUser($user);
        }

        return $response;
    }
}