<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="projects", indexes={@ORM\Index(name="code_search", columns={"code"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
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
     * @ORM\Column(type="string", length=32, options={"fixed" = false})
     */
    protected $code;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
    *
    */

    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", options={"unsigned"=true, "default"=0})
     */
    private $type = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     *
     */

    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */

    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="reviewed", type="boolean")
     */
    private $reviewed;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var bool
     *
     * @ORM\Column(name="android", type="boolean")
     */
    private $android;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set user
     *
     * @param int $user
     *
     * @return User
     */

    public function setCode($prepend = '')
    {
        $code = (empty($prepend))?md5(uniqid(mt_rand(), true)):$prepend.md5(uniqid(mt_rand(), true));
        $this->code = $code;

        return $this;
    }


    /**
     * Get id
     *
     * @return User
     */

    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param int $user
     *
     * @return User
     */

    public function setUser($id)
    {
        $this->user = $id;

        return $this;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Project
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
     * @param int $status
     *
     * @return Project
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
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
     * Set active
     *
     * @param boolean $active
     *
     * @return Project
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }


    /**
     * Set Android
     *
     * @param boolean $android
     *
     * @return Project
     */
    public function setAndroid($android)
    {
        $this->android = $android;

        return $this;
    }

    /**
     * Get android
     *
     * @return bool
     */
    public function getAndroid()
    {
        return $this->android;
    }




    /**
     * Set review
     *
     * @param boolean $reviewed
     *
     * @return Project
     */
    public function setReviewed($reviewed)
    {
        $this->reviewed = $reviewed;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getReviewed()
    {
        return $this->reviewed;
    }


    /**
     * Set created
     *
     * @ORM\PrePersist
     * @param \DateTime $created
     *
     * @return Project
     */
    public function setCreated()
    {
        if(!isset($this->created))
            $this->created = new \DateTime();

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
}

