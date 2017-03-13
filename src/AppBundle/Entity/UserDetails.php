<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDetails
 *
 * @ORM\Table(name="user_details")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserDetailsRepository")
 */
class UserDetails
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaigns")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    protected $campaignId;

    /**
     * One User has One Account
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id", nullable=true) 
    */

    protected $user;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", options={"unsigned"=true, "default"=0})
     */
    private $type = 0;    

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime")
     */

    protected $registerDate;

    /**
     * @var int
     *
     * @ORM\Column(name="pcount", type="smallint")
     */
    protected $pcount;

    /**
     * @var Boolean
     *
     * @ORM\Column(name="status", type="boolean", options={"default"=1})
     */
    protected $status = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="cubecount", type="smallint", options={"default"=0})
     */
    protected $cubecount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="activecubecount", type="smallint", options={"default"=0})
     */
    protected $activecubecount = 0;

    /**
     * @ORM\Column(type="string", length=32, options={}, nullable=true)
     */
    protected $customer = 0;

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
     * Set campaign
     *
     * @param campaign
     *
     * @return UserDetails
     */
    public function setCampaignId($campaign)
    {
        $this->campaignId = $campaign;

        return $this;
    }

    /**
     * Get cubecount
     *
     * @return int
     */
    public function getCampaignId()
    {
        return $this->campaignId;
    }


    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Set about
     *
     * @param String $about
     * @return User
     */
    public function setUser($user)
    {
        $this->user = $user;
    
        return $this;
    }


    /**
     * Increment pcount
     *
     * @param integer $cubecount
     *
     * @return UserDetails
     */
    public function decCubeCount()
    {
        $this->cubecount = $this->cubecount-=1;

        return $this;
    }

    /**
     * Increment pcount
     *
     * @param integer $cubecount
     *
     * @return UserDetails
     */
    public function incCubeCount()
    {
        $this->cubecount = $this->cubecount+=1;

        return $this;
    }


    /**
     * Set cubecount
     *
     * @param integer $cubecount
     *
     * @return UserDetails
     */
    public function setCubeCount($cubecount)
    {
        $this->cubecount = $cubecount;

        return $this;
    }

    /**
     * Get cubecount
     *
     * @return int
     */
    public function getCubeCount()
    {
        return $this->cubecount;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return User_Detail
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get activecubecount
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set activecubecount
     *
     * @param integer $activecubecount
     *
     * @return UserDetails
     */
    public function setActiveCubeCount($activecubecount)
    {
        $this->activecubecount = $activecubecount;

        return $this;
    }

    /**
     * Get activecubecount
     *
     * @return int
     */
    public function getActiveCubeCount()
    {
        return $this->activecubecount;
    }

    /**
     * Decrement activecubecount
     *
     * @param integer $activecubecount
     *
     * @return UserDetails
     */
    public function decActiveCubeCount()
    {
        $this->activecubecount = $this->activecubecount-=1;

        return $this;
    }

    /**
     * Increment activecubecount
     *
     * @param integer $activecubecount
     *
     * @return UserDetails
     */
    public function incActiveCubeCount()
    {
        $this->activecubecount = $this->activecubecount+=1;

        return $this;
    }


    /**
     * Increment pcount
     *
     * @param integer $pcount
     *
     * @return UserDetails
     */
    public function decPcount()
    {
        $this->pcount = $this->pcount-=1;

        return $this;
    }

    /**
     * Increment pcount
     *
     * @param integer $pcount
     *
     * @return UserDetails
     */
    public function incPcount()
    {
        $this->pcount = $this->pcount+=1;

        return $this;
    }


    /**
     * Set pcount
     *
     * @param integer $pcount
     *
     * @return UserDetails
     */
    public function setPcount($pcount)
    {
        $this->pcount = $pcount;

        return $this;
    }

    /**
     * Get pcount
     *
     * @return int
     */
    public function getPcount()
    {
        return $this->pcount;
    }

    /**
     * Set Register Date
     *
     * @param string $date
     *
     * @return UserDetails
     */
    public function setRegisterDate($date)
    {
        $this->registerDate = $date;

        return $this;
    }

    /**
     * Get Register Date
     *
     * @return date
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }


    /**
     * Set customer
     *
     * @param string $customer
     *
     * @return UserDetails
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

}

