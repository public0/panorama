<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Account;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * One User has One Account
     * @ORM\OneToOne(targetEntity="UserDetails")
     * @ORM\JoinColumn(name="details", referencedColumnName="id", nullable=true) 
    */

    protected $details;


    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * Set about
     *
     * @param String $about
     * @return User
     */
    public function setDetails($details)
    {
        $this->details = $details;
    
        return $this;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}