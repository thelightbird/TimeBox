<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Folder
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Folder
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
     * @ORM\ManyToOne(targetEntity="TimeBox\MainBundle\Entity\Folder")
     */
    private $parent;   

    /**
     * @ORM\ManyToOne(targetEntity="TimeBox\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean")
     */
    private $isDeleted;


    public function __construct()
    {
        $this->date = new \Datetime();
        $this->isDeleted = 0;
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
     * Set name
     *
     * @param string $name
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Folder
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
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return Folder
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set parent
     *
     * @param \TimeBox\MainBundle\Entity\Folder $parent
     * @return Folder
     */
    public function setParent(\TimeBox\MainBundle\Entity\Folder $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \TimeBox\MainBundle\Entity\Folder 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set user
     *
     * @param \TimeBox\UserBundle\Entity\User $user
     * @return Folder
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
