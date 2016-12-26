<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParametresListes
 *
 * @ORM\Table(name="parametres_listes")
 * @ORM\Entity
 */
class ParametresListes extends \Dm\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;

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
     * @ORM\Column(name="Type_Liste", type="string", length=20, nullable=true)
     */
    private $typeListe;

    /**
     * @var integer
     *
     * @ORM\Column(name="Position_Liste", type="integer", nullable=true)
     */
    private $positionListe;

    /**
     * @var string
     *
     * @ORM\Column(name="Parametre", type="string", length=30, nullable=true)
     */
    private $parametre;

    /**
     * @var string
     *
     * @ORM\Column(name="Valeur", type="string", length=20, nullable=true)
     */
    private $valeur;



    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return ParametresListes
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

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return ParametresListes
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
     * @return ParametresListes
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
     * Set typeListe
     *
     * @param string $typeListe
     *
     * @return ParametresListes
     */
    public function setTypeListe($typeListe)
    {
        $this->typeListe = $typeListe;

        return $this;
    }

    /**
     * Get typeListe
     *
     * @return string
     */
    public function getTypeListe()
    {
        return $this->typeListe;
    }

    /**
     * Set positionListe
     *
     * @param integer $positionListe
     *
     * @return ParametresListes
     */
    public function setPositionListe($positionListe)
    {
        $this->positionListe = $positionListe;

        return $this;
    }

    /**
     * Get positionListe
     *
     * @return integer
     */
    public function getPositionListe()
    {
        return $this->positionListe;
    }

    /**
     * Set parametre
     *
     * @param string $parametre
     *
     * @return ParametresListes
     */
    public function setParametre($parametre)
    {
        $this->parametre = $parametre;

        return $this;
    }

    /**
     * Get parametre
     *
     * @return string
     */
    public function getParametre()
    {
        return $this->parametre;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     *
     * @return ParametresListes
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string
     */
    public function getValeur()
    {
        return $this->valeur;
    }
}
