<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorValeurs
 *
 * @ORM\Table(name="edgecreator_valeurs")
 * @ORM\Entity
 */
class EdgecreatorValeurs
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
     * @ORM\Column(name="ID_Option", type="integer", nullable=true)
     */
    private $idOption;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Option_valeur", type="string", length=200, nullable=true)
     */
    private $optionValeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdOption(): ?int
    {
        return $this->idOption;
    }

    public function setIdOption(?int $idOption): self
    {
        $this->idOption = $idOption;

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


}
