<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeOrdreMagazines
 *
 * @ORM\Table(name="bibliotheque_ordre_magazines")
 * @ORM\Entity
 */
class BibliothequeOrdreMagazines extends \Wtd\Models\BaseModel
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
     * @var integer
     *
     * @ORM\Column(name="Ordre", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ordre;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;



    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return BibliothequeOrdreMagazines
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
     * @return BibliothequeOrdreMagazines
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
     * Set ordre
     *
     * @param integer $ordre
     *
     * @return BibliothequeOrdreMagazines
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return BibliothequeOrdreMagazines
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur
     *
     * @return integer
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }
}
