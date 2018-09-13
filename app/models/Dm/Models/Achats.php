<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achats
 *
 * @ORM\Table(name="achats", uniqueConstraints={@ORM\UniqueConstraint(name="user_date_description_unique", columns={"ID_User", "Date", "Description"})})
 * @ORM\Entity
 */
class Achats extends \Dm\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAcquisition;

    /**
     * @var int
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
     * @var string|null
     *
     * @ORM\Column(name="Style_couleur", type="string", length=9, nullable=true)
     * @deprecated
     */
    private $styleCouleur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Style_soulignement", type="string", length=0, nullable=true)
     * @deprecated
     */
    private $styleSoulignement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Style_entourage", type="string", length=0, nullable=true)
     * @deprecated
     */
    private $styleEntourage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Style_marquage", type="string", length=0, nullable=true)
     * @deprecated
     */
    private $styleMarquage;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=100, nullable=false)
     */
    private $description;



    /**
     * Get idAcquisition.
     *
     * @return int
     */
    public function getIdAcquisition()
    {
        return $this->idAcquisition;
    }

    /**
     * Set idUser.
     *
     * @param int $idUser
     *
     * @return Achats
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser.
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set date.
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
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set styleCouleur.
     *
     * @param string|null $styleCouleur
     *
     * @return Achats
     */
    public function setStyleCouleur($styleCouleur = null)
    {
        $this->styleCouleur = $styleCouleur;

        return $this;
    }

    /**
     * Get styleCouleur.
     *
     * @return string|null
     */
    public function getStyleCouleur()
    {
        return $this->styleCouleur;
    }

    /**
     * Set styleSoulignement.
     *
     * @param string|null $styleSoulignement
     *
     * @return Achats
     */
    public function setStyleSoulignement($styleSoulignement = null)
    {
        $this->styleSoulignement = $styleSoulignement;

        return $this;
    }

    /**
     * Get styleSoulignement.
     *
     * @return string|null
     */
    public function getStyleSoulignement()
    {
        return $this->styleSoulignement;
    }

    /**
     * Set styleEntourage.
     *
     * @param string|null $styleEntourage
     *
     * @return Achats
     */
    public function setStyleEntourage($styleEntourage = null)
    {
        $this->styleEntourage = $styleEntourage;

        return $this;
    }

    /**
     * Get styleEntourage.
     *
     * @return string|null
     */
    public function getStyleEntourage()
    {
        return $this->styleEntourage;
    }

    /**
     * Set styleMarquage.
     *
     * @param string|null $styleMarquage
     *
     * @return Achats
     */
    public function setStyleMarquage($styleMarquage = null)
    {
        $this->styleMarquage = $styleMarquage;

        return $this;
    }

    /**
     * Get styleMarquage.
     *
     * @return string|null
     */
    public function getStyleMarquage()
    {
        return $this->styleMarquage;
    }

    /**
     * Set description.
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
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
