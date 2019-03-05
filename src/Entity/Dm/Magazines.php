<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magazines
 *
 * @ORM\Table(name="magazines")
 * @ORM\Entity
 */
class Magazines
{
    /**
     * @var string
     *
     * @ORM\Column(name="PaysAbrege", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $paysabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAbrege", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomComplet", type="string", length=70, nullable=false)
     */
    private $nomcomplet;

    /**
     * @var string
     *
     * @ORM\Column(name="RedirigeDepuis", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $redirigedepuis = '';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="NeParaitPlus", type="boolean", nullable=true)
     */
    private $neparaitplus;

    public function getPaysabrege(): ?string
    {
        return $this->paysabrege;
    }

    public function getNomabrege(): ?string
    {
        return $this->nomabrege;
    }

    public function getNomcomplet(): ?string
    {
        return $this->nomcomplet;
    }

    public function setNomcomplet(string $nomcomplet): self
    {
        $this->nomcomplet = $nomcomplet;

        return $this;
    }

    public function getRedirigedepuis(): ?string
    {
        return $this->redirigedepuis;
    }

    public function getNeparaitplus(): ?bool
    {
        return $this->neparaitplus;
    }

    public function setNeparaitplus(?bool $neparaitplus): self
    {
        $this->neparaitplus = $neparaitplus;

        return $this;
    }


}
