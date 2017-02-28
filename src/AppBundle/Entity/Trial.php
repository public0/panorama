<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trial
 *
 * @ORM\Table(name="trial")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrialRepository")
 */
class Trial
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
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="register_date", type="datetime")
     */
    private $registerDate;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Campaigns")
     * @ORM\JoinColumn(name="campaign_id", referencedColumnName="id")
     */
    private $campaign;


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
     * Get campaign
     *
     * @return campaign
     */

    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * Set campaign
     *
     * @param int $campaign
     *
     * @return campaign
     */

    public function setCampaign($id)
    {
        $this->exporter = $id;

        return $this;
    }


    /**
     * Set email
     *
     * @param string $email
     *
     * @return Trial
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     *
     * @return Trial
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }
}

