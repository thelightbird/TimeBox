<?php

namespace TimeBox\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TimeBox\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="registrationIp", type="string", length=15, nullable=true)
     */
    private $registrationIp;

    /**
     * @var string
     *
     * @ORM\Column(name="lastLoginIp", type="string", length=15, nullable=true)
     */
    private $lastLoginIp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registrationDate", type="datetime", nullable=true)
     */
    private $registrationDate;

    /**
     * @var \Integer
     *
     * @ORM\Column(name="storage", type="integer")
     */
    private $storage;


    public function __construct()
    {
        parent::__construct();
        $this->registrationDate = new \Datetime();
        $this->storage = 0;
    }

    /**
     * Set registrationIp
     *
     * @param string $registrationIp
     * @return User
     */
    public function setRegistrationIp($registrationIp)
    {
        $this->registrationIp = $registrationIp;

        return $this;
    }

    /**
     * Get registrationIp
     *
     * @return string 
     */
    public function getRegistrationIp()
    {
        return $this->registrationIp;
    }

    /**
     * Set lastLoginIp
     *
     * @param string $lastLoginIp
     * @return User
     */
    public function setLastLoginIp($lastLoginIp)
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * Get lastLoginIp
     *
     * @return string 
     */
    public function getLastLoginIp()
    {
        return $this->lastLoginIp;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime 
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set storage
     *
     * @param integer $storage
     * @return User
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Get storage
     *
     * @return integer 
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
