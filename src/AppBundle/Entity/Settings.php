<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TextToSpeech
 *
 * @ORM\Table(name="settings")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingsRepository")
 */
class Settings
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
    * @ORM\OneToOne(targetEntity="AppBundle\Entity\Images")
    *
    */

    private $image;

    /**
     * @var array
     *
     * @ORM\Column(name="settings", type="json_array")
     */
    private $settings;


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
     * Get id
     *
     * @return Image
     */

    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set Image
     *
     * @param int $image
     *
     * @return Image
     */

    public function setImage($id)
    {
        $this->image = $id;

        return $this;
    }

    /**
     * Set settings
     *
     * @param array $settings
     *
     * @return Image
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function getSettings()
    {
        return json_encode($this->settings);
    }
}

