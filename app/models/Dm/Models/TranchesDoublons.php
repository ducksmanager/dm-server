<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * TranchesDoublons
 *
 * @ORM\Table(name="tranches_doublons", indexes={@ORM\Index(name="fk_tranche_doublon_reference", columns={"TrancheReference"})})
 * @ORM\Entity
 */
class TranchesDoublons extends \Dm\Models\BaseModel
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
     * @var \Dm\Models\TranchesPretes
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Dm\Models\TranchesPretes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TrancheReference", referencedColumnName="ID")
     * })
     */
    private $tranchereference;


    /**
     *
     * @var string
     * @deprecated
     *
     * @ORM\Column(name="NumeroReference", type="string", length=8, nullable=true)
     */
    private $numeroreference;


    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return TranchesDoublons
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set magazine
     *
     * @param string $magazine
     *
     * @return TranchesDoublons
     */
    public function setMagazine($magazine)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * Get magazine
     *
     * @return string
     */
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return TranchesDoublons
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set tranchereference
     *
     * @param \Dm\Models\TranchesPretes $tranchereference
     *
     * @return TranchesDoublons
     */
    public function setTranchereference(\Dm\Models\TranchesPretes $tranchereference = null)
    {
        $this->tranchereference = $tranchereference;

        return $this;
    }

    /**
     * Get tranchereference
     *
     * @return \Dm\Models\TranchesPretes
     */
    public function getTranchereference()
    {
        return $this->tranchereference;
    }

    /**
     * Set numeroreference
     *
     * @param string $numeroreference
     *
     * @return TranchesDoublons
     */
    public function setNumeroreference($numeroreference)
    {
        $this->numeroreference = $numeroreference;

        return $this;
    }

    /**
     * Get numeroreference
     *
     * @return string
     */
    public function getNumeroreference()
    {
        return $this->numeroreference;
    }
}
