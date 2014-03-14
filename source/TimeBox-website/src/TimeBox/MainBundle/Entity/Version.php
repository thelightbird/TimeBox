<?php

namespace TimeBox\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Version
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="TimeBox\MainBundle\Entity\VersionRepository")
 */
class Version
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
     * @ORM\ManyToOne(targetEntity="TimeBox\MainBundle\Entity\File", inversedBy="version", cascade={"remove"})
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="displayId", type="integer")
     */
    private $displayId;

    /**
     * @var integer
     *
     * @ORM\Column(name="size", type="integer")
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @Assert\File(maxSize="40000000")
     */
    private $uploadedFile;

    private $temp;


    public function __construct()
    {
        $this->date = new \Datetime();
        $this->displayId = 1;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $uploadedFile
     */
    public function setUploadedFile(UploadedFile $uploadedFile = null)
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getUploadedFile()) {
            return;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->getUploadedFile()->move(
            $this->getUploadRootDir(),
            $this->id
        );

        $this->setUploadedFile(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return $this->getUploadRootDir().'/'.$this->id;
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.$this->id;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getUploadSize()
    {
        return $this->uploadedFile->getClientSize();
    }

    /**
     * Get upload name
     *
     * @return string
     */
    public function getUploadName()
    {
        $ext = $this->uploadedFile->getClientOriginalExtension();
        $filename = $this->uploadedFile->getClientOriginalName();
        return basename($filename, '.'.$ext);
    }

    /**
     * Get upload type
     *
     * @return string
     */
    public function getUploadType()
    {
        return $this->uploadedFile->getClientOriginalExtension();
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
     * Set displayId
     *
     * @param integer $displayId
     * @return Version
     */
    public function setDisplayId($displayId)
    {
        $this->displayId = $displayId;

        return $this;
    }

    /**
     * Get displayId
     *
     * @return integer
     */
    public function getDisplayId()
    {
        return $this->displayId;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Version
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Version
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
     * Set description
     *
     * @param string $description
     * @return Version
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Version
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set file
     *
     * @param \TimeBox\MainBundle\Entity\File $file
     * @return Version
     */
    public function setFile(\TimeBox\MainBundle\Entity\File $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \TimeBox\MainBundle\Entity\File
     */
    public function getFile()
    {
        return $this->file;
    }
}
