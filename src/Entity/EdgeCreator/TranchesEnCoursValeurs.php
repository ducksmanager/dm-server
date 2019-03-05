<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursValeurs
 *
 * @ORM\Table(name="tranches_en_cours_valeurs", indexes={@ORM\Index(name="ID_Modele", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursValeurs
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
     * @var float
     *
     * @ORM\Column(name="Ordre", type="float", precision=10, scale=0, nullable=false)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom_fonction", type="string", length=30, nullable=false)
     */
    private $nomFonction;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Option_nom", type="string", length=30, nullable=true)
     */
    private $optionNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Option_valeur", type="string", length=200, nullable=true)
     */
    private $optionValeur;

    /**
     * @var \TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?float
    {
        return $this->ordre;
    }

    public function setOrdre(float $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getNomFonction(): ?string
    {
        return $this->nomFonction;
    }

    public function setNomFonction(string $nomFonction): self
    {
        $this->nomFonction = $nomFonction;

        return $this;
    }

    public function getOptionNom(): ?string
    {
        return $this->optionNom;
    }

    public function setOptionNom(?string $optionNom): self
    {
        $this->optionNom = $optionNom;

        return $this;
    }

    public function getOptionValeur(): ?string
    {
        return $this->optionValeur;
    }

    public function setOptionValeur(?string $optionValeur): self
    {
        $this->optionValeur = $optionValeur;

        return $this;
    }

    public function getIdModele(): ?TranchesEnCoursModeles
    {
        return $this->idModele;
    }

    public function setIdModele(?TranchesEnCoursModeles $idModele): self
    {
        $this->idModele = $idModele;

        return $this;
    }


}
