<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursHistoiresManquantes
 *
 * @ORM\Table(name="utilisateurs_histoires_manquantes", uniqueConstraints={@ORM\UniqueConstraint(name="missing_story_user", columns={"ID_User", "personcode", "storycode"})})
 * @ORM\Entity
 */
class UtilisateursHistoiresManquantes
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
     * @var int
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

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

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
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
