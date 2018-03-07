<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Textures
 *
 * @ORM\Table(name="textures")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TexturesRepository")
 */
class Textures
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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Images")
    *
    */
    private $image;

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
     * Set path
     *
     * @param string $path
     *
     * @return Textures
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Textures
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return image
     */
    public function getImage()
    {
        return $this->image;
    }
}

