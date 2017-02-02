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
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="pcount", type="smallint")
     */
    private $pcount;

    /**
     * @var int
     *
     * @ORM\Column(name="cubecount", type="smallint", options={"default"=0})
     */
    private $cubecount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="activecubecount", type="smallint", options={"default"=0})
     */
    private $activecubecount = 0;

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

