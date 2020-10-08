<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbonnementsSorties
 *
 * @ORM\Table(name="abonnements_sorties")
 * @ORM\Entity
 */
class AbonnementsSorties
{
    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $magazine;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date_sortie", type="date", nullable=false)
     */
    private $dateSortie;

    /**
     * @var bool
     *
     * @ORM\Column(name="Numeros_ajoutes", type="boolean", nullable=false)
     */
    private $numerosAjoutes = '0';

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function getMagazine(): ?string
    {
        return $this->magazine;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getNumerosAjoutes(): ?bool
    {
        return $this->numerosAjoutes;
    }

    public function setNumerosAjoutes(bool $numerosAjoutes): self
    {
        $this->numerosAjoutes = $numerosAjoutes;

        return $this;
    }


}
