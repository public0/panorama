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
     * @ORM\Column(type="string", length=32, options={}, nullable=true)
     */
    protected $customer;

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

