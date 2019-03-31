<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursPublicationsManquantes
 *
 * @ORM\Table(name="utilisateurs_publications_manquantes", uniqueConstraints={@ORM\UniqueConstraint(name="missing_issue_for_user", columns={"ID_User", "personcode", "storycode", "publicationcode", "issuenumber"})}, indexes={@ORM\Index(name="user_stories", columns={"ID_User", "personcode", "storycode"}), @ORM\Index(name="missing_user_issue", columns={"ID_User", "publicationcode", "issuenumber"})})
 * @ORM\Entity
 */
class UtilisateursPublicationsManquantes
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

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     */
    private $issuenumber;

    /**
     * @var int
     *
     * @ORM\Column(name="Notation", type="integer", nullable=false)
     */
    private $notation;

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

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function setPublicationcode(string $publicationcode): self
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function setIssuenumber(string $issuenumber): self
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    public function getNotation(): ?int
    {
        return $this->notation;
    }

    public function setNotation(int $notation): self
    {
        $this->notation = $notation;

        return $this;
    }


}
