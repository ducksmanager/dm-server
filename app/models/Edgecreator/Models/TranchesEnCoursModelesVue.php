<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursModelesVue
 *
 * @ORM\Table(name="tranches_en_cours_modeles_vue")
 * @ORM\Entity(readOnly=true)
 */
class TranchesEnCoursModelesVue extends \EdgeCreator\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     */
    private $magazine;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=10, nullable=false)
     */
    private $numero;

    /**
     * @var float
     *
     * @ORM\Column(name="Ordre", type="float", precision=10, scale=0, nullable=false)
     */
    private $ordre;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom_fonction", type="string", length=30, nullable=false)
     */
    private $nomFonction;

    /**
     * @var string
     *
     * @ORM\Column(name="Option_nom", type="string", length=30, nullable=true)
     */
    private $optionNom;

    /**
     * @var string
     *
     * @ORM\Column(name="Option_valeur", type="string", length=200, nullable=true)
     */
    private $optionValeur;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Modele", type="integer", nullable=false)
     */
    private $idModele;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Valeur", type="integer", nullable=false)
     */
    private $idValeur = '0';



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return TranchesEnCoursModelesVue
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
     * @return TranchesEnCoursModelesVue
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
     * Set active
     *
     * @param boolean $active
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return TranchesEnCoursModelesVue
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
     * Set ordre
     *
     * @param float $ordre
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return float
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set nomFonction
     *
     * @param string $nomFonction
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setNomFonction($nomFonction)
    {
        $this->nomFonction = $nomFonction;

        return $this;
    }

    /**
     * Get nomFonction
     *
     * @return string
     */
    public function getNomFonction()
    {
        return $this->nomFonction;
    }

    /**
     * Set optionNom
     *
     * @param string $optionNom
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setOptionNom($optionNom)
    {
        $this->optionNom = $optionNom;

        return $this;
    }

    /**
     * Get optionNom
     *
     * @return string
     */
    public function getOptionNom()
    {
        return $this->optionNom;
    }

    /**
     * Set optionValeur
     *
     * @param string $optionValeur
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setOptionValeur($optionValeur)
    {
        $this->optionValeur = $optionValeur;

        return $this;
    }

    /**
     * Get optionValeur
     *
     * @return string
     */
    public function getOptionValeur()
    {
        return $this->optionValeur;
    }

    /**
     * Set idModele
     *
     * @param integer $idModele
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setIdModele($idModele)
    {
        $this->idModele = $idModele;

        return $this;
    }

    /**
     * Get idModele
     *
     * @return integer
     */
    public function getIdModele()
    {
        return $this->idModele;
    }

    /**
     * Set idValeur
     *
     * @param integer $idValeur
     *
     * @return TranchesEnCoursModelesVue
     */
    public function setIdValeur($idValeur)
    {
        $this->idValeur = $idValeur;

        return $this;
    }

    /**
     * Get idValeur
     *
     * @return integer
     */
    public function getIdValeur()
    {
        return $this->idValeur;
    }
}
