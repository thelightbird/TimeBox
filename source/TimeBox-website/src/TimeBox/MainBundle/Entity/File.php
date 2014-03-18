<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * File
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="TimeBox\MainBundle\Entity\FileRepository")
 */
class File
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
     * @ORM\ManyToOne(targetEntity="TimeBox\MainBundle\Entity\Folder", inversedBy="files", cascade={"remove"})
     */
    private $folder;

    /**
     * @ORM\ManyToOne(targetEntity="TimeBox\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="TimeBox\MainBundle\Entity\Version", mappedBy="file", cascade={"remove"})
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="totalSize", type="integer")
     */
    private $totalSize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDeleted", type="boolean")
     */
    private $isDeleted;


    public function __construct()
    {
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
     * @return File
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
     * Set type
     *
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     * @return File
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
     * Set folder
     *
     * @param \TimeBox\MainBundle\Entity\Folder $folder
     * @return File
     */
    public function setFolder(\TimeBox\MainBundle\Entity\Folder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return \TimeBox\MainBundle\Entity\Folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set user
     *
     * @param \TimeBox\UserBundle\Entity\User $user
     * @return File
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

    /**
     * Add version
     *
     * @param \TimeBox\MainBundle\Entity\Version $version
     * @return File
     */
    public function addVersion(\TimeBox\MainBundle\Entity\Version $version)
    {
        $this->version[] = $version;

        return $this;
    }

    /**
     * Remove version
     *
     * @param \TimeBox\MainBundle\Entity\Version $version
     */
    public function removeVersion(\TimeBox\MainBundle\Entity\Version $version)
    {
        $this->version->removeElement($version);
    }

    /**
     * Get version
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set size
     *
     * @param integer $totalSize
     * @return File
     */
    public function setTotalSize($totalSize)
    {
        $this->totalSize = $totalSize;

        return $this;
    }

    /**
     * Get totalSize
     *
     * @return integer
     */
    public function getTotalSize()
    {
        return $this->totalSize;
    }

    /**
     * Get lastSize
     *
     * @return integer
     */
    public function getlastSize()
    {
        return $this->getVersion()->last()->getSize();
    }

}
