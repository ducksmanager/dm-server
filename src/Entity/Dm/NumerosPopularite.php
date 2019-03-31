<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosPopularite
 *
 * @ORM\Table(name="numeros_popularite")
 * @ORM\Entity
 */
class NumerosPopularite
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
     * @var int
     *
     * @ORM\Column(name="Popularite", type="integer", nullable=false)
     */
    private $popularite;

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
