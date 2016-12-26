<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeOptions
 *
 * @ORM\Table(name="bibliotheque_options")
 * @ORM\Entity
 */
class BibliothequeOptions extends \Dm\Models\BaseModel
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
     * @var boolean
     *
     * @ORM\Column(name="CouleurR", type="boolean", nullable=true)
     */
    private $couleurr = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="CouleurG", type="boolean", nullable=true)
     */
    private $couleurg = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="CouleurB", type="boolean", nullable=true)
     */
    private $couleurb = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="Autre", type="text", length=65535, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $autre;



    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return BibliothequeOptions
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
     * @return BibliothequeOptions
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
     * @return BibliothequeOptions
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
     * Set couleurr
     *
     * @param boolean $couleurr
     *
     * @return BibliothequeOptions
     */
    public function setCouleurr($couleurr)
    {
        $this->couleurr = $couleurr;

        return $this;
    }

    /**
     * Get couleurr
     *
     * @return boolean
     */
    public function getCouleurr()
    {
        return $this->couleurr;
    }

    /**
     * Set couleurg
     *
     * @param boolean $couleurg
     *
     * @return BibliothequeOptions
     */
    public function setCouleurg($couleurg)
    {
        $this->couleurg = $couleurg;

        return $this;
    }

    /**
     * Get couleurg
     *
     * @return boolean
     */
    public function getCouleurg()
    {
        return $this->couleurg;
    }

    /**
     * Set couleurb
     *
     * @param boolean $couleurb
     *
     * @return BibliothequeOptions
     */
    public function setCouleurb($couleurb)
    {
        $this->couleurb = $couleurb;

        return $this;
    }

    /**
     * Get couleurb
     *
     * @return boolean
     */
    public function getCouleurb()
    {
        return $this->couleurb;
    }

    /**
     * Set autre
     *
     * @param string $autre
     *
     * @return BibliothequeOptions
     */
    public function setAutre($autre)
    {
        $this->autre = $autre;

        return $this;
    }

    /**
     * Get autre
     *
     * @return string
     */
    public function getAutre()
    {
        return $this->autre;
    }
}
