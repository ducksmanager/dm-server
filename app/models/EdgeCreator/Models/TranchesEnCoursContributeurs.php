<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * TranchesEnCoursContributeurs
 *
 * @ORM\Table(name="tranches_en_cours_contributeurs", indexes={@ORM\Index(name="IDX_1D8956AC4A1ED576", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursContributeurs extends \EdgeCreator\Models\BaseModel
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
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     */
    private $idUtilisateur;

    /**
     * @var TranchesEnCoursModeles
     *
     * @ManyToOne(targetEntity="TranchesEnCoursModeles", inversedBy="contributeurs")
     * @JoinColumn(name="ID_Modele", referencedColumnName="ID")
     */
    private $modele;

    /**
     * @var string
     *
     * @ORM\Column(name="contribution", type="string", nullable=false)
     */
    private $contribution;



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
     * @return TranchesEnCoursContributeurs
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
     * Set modele
     *
     * @param TranchesEnCoursModeles $modele
     *
     * @return TranchesEnCoursContributeurs
     */
    public function setModele($modele = null)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return TranchesEnCoursModeles
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set contribution
     *
     * @param string $contribution
     *
     * @return TranchesEnCoursContributeurs
     */
    public function setContribution($contribution)
    {
        $this->contribution = $contribution;

        return $this;
    }

    /**
     * Get contribution
     *
     * @return string
     */
    public function getContribution()
    {
        return $this->contribution;
    }
}
