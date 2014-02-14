<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Link
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="TimeBox\MainBundle\Entity\Version")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="TimeBox\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


    public function __construct()
    {
        $this->date = new \Datetime();
    }

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
     * Set date
     *
     * @param \DateTime $date
     * @return Link
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set version
     *
     * @param \TimeBox\MainBundle\Entity\Version $version
     * @return Link
     */
    public function setVersion(\TimeBox\MainBundle\Entity\Version $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return \TimeBox\MainBundle\Entity\Version 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set user
     *
     * @param \TimeBox\UserBundle\Entity\User $user
     * @return Link
     */
    public function setUser(\TimeBox\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \TimeBox\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
