<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bouquineries
 *
 * @ORM\Table(name="bouquineries")
 * @ORM\Entity
 */
class Bouquineries extends \Wtd\Models\BaseModel
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
     * @ORM\Column(name="Nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse", type="text", length=65535, nullable=false)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseComplete", type="text", length=65535, nullable=false)
     */
    private $adressecomplete;

    /**
     * @var integer
     *
     * @ORM\Column(name="CodePostal", type="integer", nullable=false)
     */
    private $codepostal;

    /**
     * @var string
     *
     * @ORM\Column(name="Ville", type="string", length=20, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=20, nullable=false)
     */
    private $pays = 'France';

    /**
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=false)
     */
    private $commentaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var float
     *
     * @ORM\Column(name="CoordX", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordx = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="CoordY", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordy = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="DateAjout", type="integer", nullable=false)
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var boolean
     *
     * @ORM\Column(name="Actif", type="boolean", nullable=false)
     */
    private $actif = '1';



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
     * Set nom
     *
     * @param string $nom
     *
     * @return Bouquineries
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Bouquineries
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set adressecomplete
     *
     * @param string $adressecomplete
     *
     * @return Bouquineries
     */
    public function setAdressecomplete($adressecomplete)
    {
        $this->adressecomplete = $adressecomplete;

        return $this;
    }

    /**
     * Get adressecomplete
     *
     * @return string
     */
    public function getAdressecomplete()
    {
        return $this->adressecomplete;
    }

    /**
     * Set codepostal
     *
     * @param integer $codepostal
     *
     * @return Bouquineries
     */
    public function setCodepostal($codepostal)
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    /**
     * Get codepostal
     *
     * @return integer
     */
    public function getCodepostal()
    {
        return $this->codepostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Bouquineries
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Bouquineries
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
     * Set commentaire
     *
     * @param string $commentaire
     *
     * @return Bouquineries
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return Bouquineries
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
     * Set coordx
     *
     * @param float $coordx
     *
     * @return Bouquineries
     */
    public function setCoordx($coordx)
    {
        $this->coordx = $coordx;

        return $this;
    }

    /**
     * Get coordx
     *
     * @return float
     */
    public function getCoordx()
    {
        return $this->coordx;
    }

    /**
     * Set coordy
     *
     * @param float $coordy
     *
     * @return Bouquineries
     */
    public function setCoordy($coordy)
    {
        $this->coordy = $coordy;

        return $this;
    }

    /**
     * Get coordy
     *
     * @return float
     */
    public function getCoordy()
    {
        return $this->coordy;
    }

    /**
     * Set dateajout
     *
     * @param integer $dateajout
     *
     * @return Bouquineries
     */
    public function setDateajout($dateajout)
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    /**
     * Get dateajout
     *
     * @return integer
     */
    public function getDateajout()
    {
        return $this->dateajout;
    }

    /**
     * Set actif
     *
     * @param boolean $actif
     *
     * @return Bouquineries
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif
     *
     * @return boolean
     */
    public function getActif()
    {
        return $this->actif;
    }
}
