<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesDoublons
 *
 * @ORM\Table(name="tranches_doublons", indexes={@ORM\Index(name="tranches_doublons_tranches_pretes_ID_fk", columns={"TrancheReference"})})
 * @ORM\Entity
 */
class TranchesDoublons
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
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="NumeroReference", type="string", length=8, nullable=false)
     */
    private $numeroreference;

    /**
     * @var TranchesPretes
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="TranchesPretes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TrancheReference", referencedColumnName="ID")
     * })
     */
    private $tranchereference;

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNumeroreference(): ?string
    {
        return $this->numeroreference;
    }

    public function setNumeroreference(string $numeroreference): self
    {
        $this->numeroreference = $numeroreference;

        return $this;
    }

    public function getTranchereference(): ?TranchesPretes
    {
        return $this->tranchereference;
    }

    public function setTranchereference(?TranchesPretes $tranchereference): self
    {
        $this->tranchereference = $tranchereference;

        return $this;
    }


}
