<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosPopularite
 *
 * @ORM\Table(name="numeros_popularite")
 * @ORM\Entity
 */
class NumerosPopularite extends \Dm\Models\BaseModel
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



    /**
     * Set pays.
     *
     * @param string $pays
     *
     * @return NumerosPopularite
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays.
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set magazine.
     *
     * @param string $magazine
     *
     * @return NumerosPopularite
     */
    public function setMagazine($magazine)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * Get magazine.
     *
     * @return string
     */
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
     * Set numero.
     *
     * @param string $numero
     *
     * @return NumerosPopularite
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set popularite.
     *
     * @param int $popularite
     *
     * @return NumerosPopularite
     */
    public function setPopularite($popularite)
    {
        $this->popularite = $popularite;

        return $this;
    }

    /**
     * Get popularite.
     *
     * @return int
     */
    public function getPopularite()
    {
        return $this->popularite;
    }
}
