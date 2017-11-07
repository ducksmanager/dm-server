<?php

namespace DmStats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuteursHistoires
 *
 * @ORM\Table(name="auteurs_histoires", indexes={@ORM\Index(name="index_storycode", columns={"storycode"})})
 * @ORM\Entity
 */
class AuteursHistoires extends \DmStats\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="personcode", type="string", length=22, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $personcode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storycode;



    /**
     * Set personcode
     *
     * @param string $personcode
     *
     * @return AuteursHistoires
     */
    public function setPersoncode($personcode)
    {
        $this->personcode = $personcode;

        return $this;
    }

    /**
     * Get personcode
     *
     * @return string
     */
    public function getPersoncode()
    {
        return $this->personcode;
    }

    /**
     * Set storycode
     *
     * @param string $storycode
     *
     * @return AuteursHistoires
     */
    public function setStorycode($storycode)
    {
        $this->storycode = $storycode;

        return $this;
    }

    /**
     * Get storycode
     *
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }
}
