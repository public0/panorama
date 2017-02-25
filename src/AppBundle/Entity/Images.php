<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Images
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImagesRepository")
 */
class Images
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Project")
    *
    */

    private $project;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */

    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=20)
     */

    private $title;


    /**
     * @var smallint
     *
     * @ORM\Column(name="width", type="smallint")
     */

    private $width;

    /**
     * @var smallint
     *
     * @ORM\Column(name="height", type="smallint")
     */

    private $height;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Exporter")
     * @ORM\JoinColumn(name="exporter_id", referencedColumnName="id")
     */
    private $exporter;


    /**
     * Get id
     *
     * @return int
     */


    /**
     * @var \smallint
     *
     * @ORM\Column(name="plan", type="smallint")
     */

    private $plan;

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set plan
     *
     * @param string $order
     *
     * @return Images
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Images
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * Set Width
     *
     * @param smallint $title
     *
     * @return Images
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Height
     *
     * @param smallint $height
     *
     * @return Images
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Images
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
     * Get id
     *
     * @return exporter
     */

    public function getExporter()
    {
        return $this->exporter;
    }

    /**
     * Set user
     *
     * @param int $user
     *
     * @return Exporter
     */

    public function setExporter($id)
    {
        $this->exporter = $id;

        return $this;
    }


    /**
     * Get id
     *
     * @return Project
     */

    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param int $user
     *
     * @return Project
     */

    public function setProject($id)
    {
        $this->project = $id;

        return $this;
    }


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Images
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Images
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
}

