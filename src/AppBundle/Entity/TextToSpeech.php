<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TextToSpeech
 *
 * @ORM\Table(name="text_to_speech")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TextToSpeechRepository")
 */
class TextToSpeech
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
    * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Images")
    *
    */

    private $image;


    /**
     * @var string
     *
     * @ORM\Column(name="event", type="string", length=255)
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var array
     *
     * @ORM\Column(name="vector", type="json_array")
     */
    private $vector;


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
     * Set event
     *
     * @param string $event
     *
     * @return TextToSpeech
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return TextToSpeech
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set vector
     *
     * @param array $vector
     *
     * @return TextToSpeech
     */
    public function setVector($vector)
    {
        $this->vector = $vector;

        return $this;
    }

    /**
     * Get vector
     *
     * @return array
     */
    public function getVector()
    {
        return json_encode($this->vector);
    }
}

