<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorModeles2
 *
 * @ORM\Table(name="edgecreator_modeles2")
 * @ORM\Entity
 */
class EdgecreatorModeles2 extends \EdgeCreator\Models\BaseModel
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
     * @ORM\Column(name="Option_nom", type="string", length=20, nullable=true)
     */
    private $optionNom;



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
     * Set pays
     *
     * @param string $pays
     *
     * @return EdgecreatorModeles2
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
     * @return EdgecreatorModeles2
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
     * @param float $ordre
     *
     * @return EdgecreatorModeles2
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
     * @return EdgecreatorModeles2
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
     * @return EdgecreatorModeles2
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
}
