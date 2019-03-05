<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bouquineries
 *
 * @ORM\Table(name="bouquineries")
 * @ORM\Entity
 */
class Bouquineries
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
     * @ORM\Column(name="Nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Adresse", type="text", length=65535, nullable=true)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseComplete", type="text", length=65535, nullable=false)
     */
    private $adressecomplete;

    /**
     * @var int|null
     *
     * @ORM\Column(name="CodePostal", type="integer", nullable=true)
     */
    private $codepostal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Ville", type="string", length=20, nullable=true)
     */
    private $ville;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Pays", type="string", length=20, nullable=true, options={"default"="France"})
     */
    private $pays = 'France';

    /**
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=false)
     */
    private $commentaire;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var float
     *
     * @ORM\Column(name="CoordX", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordx = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="CoordY", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordy = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAjout", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var bool
     *
     * @ORM\Column(name="Actif", type="boolean", nullable=false, options={"default"="1"})
     */
    private $actif = '1';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getAdressecomplete(): ?string
    {
        return $this->adressecomplete;
    }

    public function setAdressecomplete(string $adressecomplete): self
    {
        $this->adressecomplete = $adressecomplete;

        return $this;
    }

    public function getCodepostal(): ?int
    {
        return $this->codepostal;
    }

    public function setCodepostal(?int $codepostal): self
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
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

    public function getCoordx(): ?float
    {
        return $this->coordx;
    }

    public function setCoordx(float $coordx): self
    {
        $this->coordx = $coordx;

        return $this;
    }

    public function getCoordy(): ?float
    {
        return $this->coordy;
    }

    public function setCoordy(float $coordy): self
    {
        $this->coordy = $coordy;

        return $this;
    }

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(\DateTimeInterface $dateajout): self
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }


}
