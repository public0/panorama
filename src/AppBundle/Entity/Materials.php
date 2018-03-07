<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Materials
 *
 * @ORM\Table(name="materials")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MaterialsRepository")
 */
class Materials
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
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\Images")
    *
    */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;


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
     * @return Materials
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
     * @param Images $image
     *
     * @return Materials
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get Images
     *
     * @return Images
     */
    public function getImage()
    {
        return $this->image;
    }

}

