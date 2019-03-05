<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuteursHistoires
 *
 * @ORM\Table(name="auteurs_histoires", uniqueConstraints={@ORM\UniqueConstraint(name="personcode", columns={"personcode", "storycode"})}, indexes={@ORM\Index(name="index_storycode", columns={"storycode"})})
 * @ORM\Entity
 */
class AuteursHistoires
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="personcode", type="string", length=22, nullable=false)
     */
    private $personcode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     */
    private $storycode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersoncode(): ?string
    {
        return $this->personcode;
    }

    public function setPersoncode(string $personcode): self
    {
        $this->personcode = $personcode;

        return $this;
    }

    public function getStorycode(): ?string
    {
        return $this->storycode;
    }

    public function setStorycode(string $storycode): self
    {
        $this->storycode = $storycode;

        return $this;
    }


}
