<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursValeurs
 *
 * @ORM\Table(name="tranches_en_cours_valeurs", indexes={@ORM\Index(name="ID_Modele", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursValeurs extends \EdgeCreator\Models\BaseModel
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
     * @var \EdgeCreator\Models\TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="EdgeCreator\Models\TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;



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
     * Set ordre
     *
     * @param float $ordre
     *
     * @return TranchesEnCoursValeurs
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
     * @return TranchesEnCoursValeurs
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
     * @return TranchesEnCoursValeurs
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
     * @return TranchesEnCoursValeurs
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
     * @param \EdgeCreator\Models\TranchesEnCoursModeles $idModele
     *
     * @return TranchesEnCoursValeurs
     */
    public function setIdModele(\EdgeCreator\Models\TranchesEnCoursModeles $idModele = null)
    {
        $this->idModele = $idModele;

        return $this;
    }

    /**
     * Get idModele
     *
     * @return \EdgeCreator\Models\TranchesEnCoursModeles
     */
    public function getIdModele()
    {
        return $this->idModele;
    }
}
