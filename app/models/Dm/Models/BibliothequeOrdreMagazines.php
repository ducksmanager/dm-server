<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeOrdreMagazines
 *
 * @ORM\Table(name="bibliotheque_ordre_magazines", uniqueConstraints={@ORM\UniqueConstraint(name="bibliotheque_ordre_magazines_uindex", columns={"ID_Utilisateur", "publicationcode"})})
 * @ORM\Entity
 */
class BibliothequeOrdreMagazines extends \Dm\Models\BaseModel
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
     * @var int
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode;

    /**
     * @var int
     *
     * @ORM\Column(name="Ordre", type="integer", nullable=false)
     */
    private $ordre;



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
     * @param int $idUtilisateur
     *
     * @return BibliothequeOrdreMagazines
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur.
     *
     * @return int
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set publicationcode.
     *
     * @param string $publicationcode
     *
     * @return BibliothequeOrdreMagazines
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode.
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set ordre.
     *
     * @param int $ordre
     *
     * @return BibliothequeOrdreMagazines
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre.
     *
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }
}
