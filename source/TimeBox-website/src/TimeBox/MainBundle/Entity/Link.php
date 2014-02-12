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
    private $versionId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;



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
     * Set versionId
     *
     * @param \TimeBox\MainBundle\Entity\Version $versionId
     * @return Link
     */
    public function setVersionId(\TimeBox\MainBundle\Entity\Version $versionId = null)
    {
        $this->versionId = $versionId;

        return $this;
    }

    /**
     * Get versionId
     *
     * @return \TimeBox\MainBundle\Entity\Version 
     */
    public function getVersionId()
    {
        return $this->versionId;
    }
}
