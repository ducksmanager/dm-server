<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesDoublons
 *
 * @ORM\Table(name="tranches_doublons")
 * @ORM\Entity
 */
class TranchesDoublons extends \Wtd\Models\BaseModel
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
     * @var string
     *
     * @ORM\Column(name="NumeroReference", type="string", length=8, nullable=false)
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
