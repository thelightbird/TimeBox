<?php
namespace TimeBox\UserBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use TimeBox\UserBundle\Entity\User;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserBundle;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LoginManager implements EventSubscriberInterface
{  
    /** @var \Symfony\Component\Security\Core\SecurityContext */
    private $securityContext;

    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /**
     * Constructor
     *
     * @param SecurityContext $securityContext
     * @param Doctrine        $doctrine
     */
    public function __construct(SecurityContext $securityContext, Doctrine $doctrine)
    {
        $this->securityContext = $securityContext;
        $this->em = $doctrine->getManager();
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onSecurityImplicitLogin'
        );
    }

    public function onSecurityImplicitLogin(UserEvent $event)
    {
        $this->updateUserInfos($event);
    }

    public function onSecurityInteractivelogin(InteractiveLoginEvent $event)
    {
        $this->updateUserInfos($event);
    }

    public function updateUserInfos($event) {
        $user = $this->securityContext->getToken()->getUser();

        if (!is_object($user)) {
            throw new AccessDeniedException('You are not logged in.');
        }

        $request = $event->getRequest();
        $user->setLastLogin(new \Datetime());
        $user->setLastLoginIp($request->getClientIp());

        $this->em->persist($user);
        $this->em->flush();
    }
}