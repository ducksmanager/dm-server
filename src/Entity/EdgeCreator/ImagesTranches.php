<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesTranches
 *
 * @ORM\Table(name="images_tranches", uniqueConstraints={@ORM\UniqueConstraint(name="images_tranches_Hash_uindex", columns={"Hash"})})
 * @ORM\Entity
 */
class ImagesTranches
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
     * @var int|null
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Hash", type="string", length=40, nullable=true)
     */
    private $hash;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="DateHeure", type="datetime", nullable=true)
     */
    private $dateheure;

    /**
     * @var string
     *
     * @ORM\Column(name="NomFichier", type="string", length=255, nullable=false)
     */
    private $nomfichier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?int $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getDateheure(): ?\DateTimeInterface
    {
        return $this->dateheure;
    }

    public function setDateheure(?\DateTimeInterface $dateheure): self
    {
        $this->dateheure = $dateheure;

        return $this;
    }

    public function getNomfichier(): ?string
    {
        return $this->nomfichier;
    }

    public function setNomfichier(string $nomfichier): self
    {
        $this->nomfichier = $nomfichier;

        return $this;
    }


}
