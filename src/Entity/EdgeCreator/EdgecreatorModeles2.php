<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorModeles2
 *
 * @ORM\Table(name="edgecreator_modeles2")
 * @ORM\Entity
 */
class EdgecreatorModeles2
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
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     */
    private $magazine;

    /**
     * @var int
     *
     * @ORM\Column(name="Ordre", type="integer", nullable=false)
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
     * @ORM\Column(name="Option_nom", type="string", length=20, nullable=true)
     */
    private $optionNom;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getMagazine(): ?string
    {
        return $this->magazine;
    }

    public function setMagazine(string $magazine): self
    {
        $this->magazine = $magazine;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
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


}
