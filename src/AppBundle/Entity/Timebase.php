<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timebase
 *
 * @ORM\Table(name="timebase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TimebaseRepository")
 */
class Timebase
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
     * @ORM\Column(name="initstamp", type="integer")
     */
    private $initstamp;

    /**
     * @var int
     *
     * @ORM\Column(name="stamp36", type="integer")
     */
    private $stamp36;


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
     * Set initstamp
     *
     * @param integer $initstamp
     *
     * @return Timebase
     */
    public function setInitstamp($initstamp)
    {
        $this->initstamp = $initstamp;

        return $this;
    }

    /**
     * Get initstamp
     *
     * @return int
     */
    public function getInitstamp()
    {
        return $this->initstamp;
    }

    /**
     * Set stamp36
     *
     * @param integer $stamp36
     *
     * @return Timebase
     */
    public function setStamp36($stamp36)
    {
        $this->stamp36 = $stamp36;

        return $this;
    }

    /**
     * Get stamp36
     *
     * @return int
     */
    public function getStamp36()
    {
        return $this->stamp36;
    }
}

