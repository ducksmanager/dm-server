<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesTranches
 *
 * @ORM\Table(name="images_tranches", uniqueConstraints={@ORM\UniqueConstraint(name="images_tranches_Hash_uindex", columns={"Hash"})})
 * @ORM\Entity
 */
class ImagesTranches extends \Edgecreator\Models\BaseModel
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
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="Hash", type="string", length=40, nullable=true)
     */
    private $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateHeure", type="datetime", nullable=true)
     */
    private $dateheure;

    /**
     * @var string
     *
     * @ORM\Column(name="NomFichier", type="string", length=255, nullable=false)
     */
    private $nomfichier;



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
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return ImagesTranches
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
     * Set hash
     *
     * @param string $hash
     *
     * @return ImagesTranches
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set dateheure
     *
     * @param \DateTime $dateheure
     *
     * @return ImagesTranches
     */
    public function setDateheure($dateheure)
    {
        $this->dateheure = $dateheure;

        return $this;
    }

    /**
     * Get dateheure
     *
     * @return \DateTime
     */
    public function getDateheure()
    {
        return $this->dateheure;
    }

    /**
     * Set nomfichier
     *
     * @param string $nomfichier
     *
     * @return ImagesTranches
     */
    public function setNomfichier($nomfichier)
    {
        $this->nomfichier = $nomfichier;

        return $this;
    }

    /**
     * Get nomfichier
     *
     * @return string
     */
    public function getNomfichier()
    {
        return $this->nomfichier;
    }
}
