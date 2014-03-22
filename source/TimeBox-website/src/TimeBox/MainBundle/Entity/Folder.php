<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Folder
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="TimeBox\MainBundle\Entity\FolderRepository")
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
     * @ORM\OneToMany(targetEntity="TimeBox\MainBundle\Entity\Folder", mappedBy="parent", cascade={"persist", "remove"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="TimeBox\MainBundle\Entity\Folder", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="TimeBox\MainBundle\Entity\File", mappedBy="folder")
     */
    private $files;

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
        $this->files = new ArrayCollection();
        $this->folder = new ArrayCollection();
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
     * Add children
     *
     * @param \TimeBox\MainBundle\Entity\Folder $children
     * @return Folder
     */
    public function addChild(\TimeBox\MainBundle\Entity\Folder $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \TimeBox\MainBundle\Entity\Folder $children
     */
    public function removeChild(\TimeBox\MainBundle\Entity\Folder $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
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
     * Add files
     *
     * @param \TimeBox\MainBundle\Entity\File $files
     * @return Folder
     */
    public function addFile(\TimeBox\MainBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \TimeBox\MainBundle\Entity\File $files
     */
    public function removeFile(\TimeBox\MainBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
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
