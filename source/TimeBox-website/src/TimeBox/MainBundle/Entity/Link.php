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
     * @ORM\ManyToOne(targetEntity="TimeBox\UserBundle\Entity\User")
     */
    private $userId;

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

    /**
     * Set userId
     *
     * @param \TimeBox\UserBundle\Entity\User $userId
     * @return Link
     */
    public function setUserId(\TimeBox\UserBundle\Entity\User $userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return \TimeBox\UserBundle\Entity\User 
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
