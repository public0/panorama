<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Campaigns
 *
 * @ORM\Table(name="campaigns")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CampaignsRepository")
 */
class Campaigns
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="datetime")
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="datetime")
     */
    private $enddate;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="days", type="smallint", options={"unsigned"=true, "default"=0})
     */
    private $days;


    /**
     * @var int
     *
     * @ORM\Column(name="max", type="smallint", options={"comment":"Max number of users that can register to this campaign.", "unsigned"=true, "default"=0})
     */
    private $max;

    /**
     * @var int
     *
     * @ORM\Column(name="current_max", type="smallint", options={"comment":"Surrent number of users registered to one campaign.","unsigned"=true, "default"=0})
     */

    private $currentMax;

    /**
     * Get id
     *
     * @return int
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Campaigns
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
     * Set startdate
     *
     * @param \DateTime $startdate
     *
     * @return Campaigns
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     *
     * @return Campaigns
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Campaigns
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set max
     *
     * @param integer $max
     *
     * @return Campaigns
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Get max
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Increment currentMax
     *
     * @param integer $currentMax
     *
     * @return UserDetails
     */
    public function decCurrentMax()
    {
        $this->currentMax = $this->currentMax-=1;

        return $this;
    }

    /**
     * Increment currentMax
     *
     * @param integer $cubecount
     *
     * @return UserDetails
     */
    public function incCurrentMax()
    {
        $this->currentMax = $this->currentMax+=1;

        return $this;
    }
}

