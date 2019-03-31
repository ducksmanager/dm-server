<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursPublicationsSuggerees
 *
 * @ORM\Table(name="utilisateurs_publications_suggerees", uniqueConstraints={@ORM\UniqueConstraint(name="suggested_issue_for_user", columns={"ID_User", "publicationcode", "issuenumber"})}, indexes={@ORM\Index(name="suggested_issue_user", columns={"ID_User"})})
 * @ORM\Entity
 */
class UtilisateursPublicationsSuggerees
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
     * @ORM\Column(name="Score", type="integer", nullable=false)
     */
    private $score;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(?int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
