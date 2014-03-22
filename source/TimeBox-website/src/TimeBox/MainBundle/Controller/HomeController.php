<?php

namespace TimeBox\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * Redirect user to user's files page or registration page if user is logged or not.
     *
     */
    public function indexAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('time_box_main_file'));
        }
        return $this->redirect($this->generateUrl('fos_user_registration_register'));
    }
}
