<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountDetails
 *
 * @ORM\Table(name="account_details")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountDetailsRepository")
 */
class AccountDetails
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
     * @ORM\Column(name="max_project_count", type="smallint")
     */
    private $maxProjectCount;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer")
     */
    private $price;


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
     * Set maxProjectCount
     *
     * @param integer $maxProjectCount
     *
     * @return AccountDetails
     */
    public function setMaxProjectCount($maxProjectCount)
    {
        $this->maxProjectCount = $maxProjectCount;

        return $this;
    }

    /**
     * Get maxProjectCount
     *
     * @return int
     */
    public function getMaxProjectCount()
    {
        return $this->maxProjectCount;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return AccountDetails
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }
}

