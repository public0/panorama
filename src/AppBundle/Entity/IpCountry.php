<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IpCountry
 *
 * @ORM\Table(name="ip_country")
 * @ORM\Table(indexes={@ORM\Index(name="end_id_idx", columns={"end_ip"})})     
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IpCountryRepository")
 */
class IpCountry
{

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="start_ip", type="bigint")
     */
    private $startIp;

    /**
     * @var int
     *
     * @ORM\Column(name="end_ip", type="bigint")
     */
    private $endIp;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=2, options={"collation" : "utf8_unicode_ci", "charset" : "utf8"})
     */
    private $countryCode;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->startIp;
    }

    /**
     * Set startIp
     *
     * @param integer $startIp
     *
     * @return IpCountry
     */
    public function setStartIp($startIp)
    {
        $this->startIp = $startIp;

        return $this;
    }

    /**
     * Get startIp
     *
     * @return int
     */
    public function getStartIp()
    {
        return $this->startIp;
    }

    /**
     * Set endIp
     *
     * @param integer $endIp
     *
     * @return IpCountry
     */
    public function setEndIp($endIp)
    {
        $this->endIp = $endIp;

        return $this;
    }

    /**
     * Get endIp
     *
     * @return int
     */
    public function getEndIp()
    {
        return $this->endIp;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return IpCountry
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}

