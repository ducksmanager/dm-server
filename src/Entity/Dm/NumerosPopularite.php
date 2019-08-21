<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosPopularite
 *
 * @ORM\Table(name="numeros_popularite", uniqueConstraints={@ORM\UniqueConstraint(name="numeros_popularite_unique", columns={"Pays", "Magazine", "Numero"})})
 * @ORM\Entity
 */
class NumerosPopularite
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
     * @var int
     *
     * @ORM\Column(name="Popularite", type="integer", nullable=false)
     */
    private $popularite;

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

    public function getPopularite(): ?int
    {
        return $this->popularite;
    }

    public function setPopularite(int $popularite): self
    {
        $this->popularite = $popularite;

        return $this;
    }


}
