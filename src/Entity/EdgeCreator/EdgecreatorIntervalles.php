<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorIntervalles
 *
 * @ORM\Table(name="edgecreator_intervalles", indexes={@ORM\Index(name="index_intervalles", columns={"ID_Valeur", "Numero_debut", "Numero_fin", "username"})})
 * @ORM\Entity
 */
class EdgecreatorIntervalles
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
     * @ORM\Column(name="ID_Valeur", type="integer", nullable=false)
     */
    private $idValeur;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero_debut", type="string", length=10, nullable=false)
     */
    private $numeroDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero_fin", type="string", length=10, nullable=false)
     */
    private $numeroFin;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=false)
     */
    private $username;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdValeur(): ?int
    {
        return $this->idValeur;
    }

    public function setIdValeur(int $idValeur): self
    {
        $this->idValeur = $idValeur;

        return $this;
    }

    public function getNumeroDebut(): ?string
    {
        return $this->numeroDebut;
    }

    public function setNumeroDebut(string $numeroDebut): self
    {
        $this->numeroDebut = $numeroDebut;

        return $this;
    }

    public function getNumeroFin(): ?string
    {
        return $this->numeroFin;
    }

    public function setNumeroFin(string $numeroFin): self
    {
        $this->numeroFin = $numeroFin;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }


}
