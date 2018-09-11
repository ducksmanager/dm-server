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
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Hash", type="string", length=40, nullable=true)
     */
    private $hash;

    /**
     * @var \DateTime|null
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUtilisateur.
     *
     * @param int|null $idUtilisateur
     *
     * @return ImagesTranches
     */
    public function setIdUtilisateur($idUtilisateur = null)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur.
     *
     * @return int|null
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set hash.
     *
     * @param string|null $hash
     *
     * @return ImagesTranches
     */
    public function setHash($hash = null)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash.
     *
     * @return string|null
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set dateheure.
     *
     * @param \DateTime|null $dateheure
     *
     * @return ImagesTranches
     */
    public function setDateheure($dateheure = null)
    {
        $this->dateheure = $dateheure;

        return $this;
    }

    /**
     * Get dateheure.
     *
     * @return \DateTime|null
     */
    public function getDateheure()
    {
        return $this->dateheure;
    }

    /**
     * Set nomfichier.
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
     * Get nomfichier.
     *
     * @return string
     */
    public function getNomfichier()
    {
        return $this->nomfichier;
    }
}
