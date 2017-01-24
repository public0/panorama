<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account
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
     * One Account has One Account type
     * @ORM\OneToOne(targetEntity="AccountDetails")
     * @ORM\JoinColumn(name="accountType", referencedColumnName="id") 
    */
    private $accountType;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=25)
     */
    private $name;


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
     * @return Account
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
     * Get Account Details
     *
     * @return User
     */

    public function getAccountType()
    {
        return $this->accountType;
    }

    /**
     * Set user
     *
     * @param int $acc
     *
     * @return Account Details
     */

    public function setAccountType($acc)
    {
        $this->accountType = $acc;

        return $this;
    }
}

