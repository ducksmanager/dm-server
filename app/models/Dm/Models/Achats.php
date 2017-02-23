<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achats
 *
 * @ORM\Table(name="achats")
 * @ORM\Entity
 */
class Achats extends \Dm\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAcquisition;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_couleur", type="string", length=9, nullable=true)
     */
    private $styleCouleur;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_soulignement", type="string", nullable=true)
     */
    private $styleSoulignement;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_entourage", type="string", nullable=true)
     */
    private $styleEntourage;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_marquage", type="string", nullable=true)
     */
    private $styleMarquage;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=100, nullable=false)
     */
    private $description;



    /**
     * Set idAcquisition
     *
     * @param integer $idAcquisition
     *
     * @return Achats
     */
    public function setIdAcquisition($idAcquisition)
    {
        $this->idAcquisition = $idAcquisition;

        return $this;
    }

    /**
     * Get idAcquisition
     *
     * @return integer
     */
    public function getIdAcquisition()
    {
        return $this->idAcquisition;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Achats
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Achats
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set styleCouleur
     *
     * @param string $styleCouleur
     *
     * @return Achats
     */
    public function setStyleCouleur($styleCouleur)
    {
        $this->styleCouleur = $styleCouleur;

        return $this;
    }

    /**
     * Get styleCouleur
     *
     * @return string
     */
    public function getStyleCouleur()
    {
        return $this->styleCouleur;
    }

    /**
     * Set styleSoulignement
     *
     * @param string $styleSoulignement
     *
     * @return Achats
     */
    public function setStyleSoulignement($styleSoulignement)
    {
        $this->styleSoulignement = $styleSoulignement;

        return $this;
    }

    /**
     * Get styleSoulignement
     *
     * @return string
     */
    public function getStyleSoulignement()
    {
        return $this->styleSoulignement;
    }

    /**
     * Set styleEntourage
     *
     * @param string $styleEntourage
     *
     * @return Achats
     */
    public function setStyleEntourage($styleEntourage)
    {
        $this->styleEntourage = $styleEntourage;

        return $this;
    }

    /**
     * Get styleEntourage
     *
     * @return string
     */
    public function getStyleEntourage()
    {
        return $this->styleEntourage;
    }

    /**
     * Set styleMarquage
     *
     * @param string $styleMarquage
     *
     * @return Achats
     */
    public function setStyleMarquage($styleMarquage)
    {
        $this->styleMarquage = $styleMarquage;

        return $this;
    }

    /**
     * Get styleMarquage
     *
     * @return string
     */
    public function getStyleMarquage()
    {
        return $this->styleMarquage;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Achats
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
