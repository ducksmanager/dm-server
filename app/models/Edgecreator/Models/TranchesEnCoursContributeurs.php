<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursContributeurs
 *
 * @ORM\Table(name="tranches_en_cours_contributeurs", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_en_cours_contributeurs__unique", columns={"ID_Modele", "ID_Utilisateur", "contribution"})}, indexes={@ORM\Index(name="IDX_1D8956AC4A1ED576", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursContributeurs extends \Edgecreator\Models\BaseModel
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
     * @ORM\Column(name="contribution", type="string", length=0, nullable=false)
     */
    private $contribution;

    /**
     * @var \Edgecreator\Models\TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="Edgecreator\Models\TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;



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
     * @return TranchesEnCoursContributeurs
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
     * Set contribution.
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
     * Get contribution.
     *
     * @return string
     */
    public function getContribution()
    {
        return $this->contribution;
    }

    /**
     * Set idModele.
     *
     * @param \Edgecreator\Models\TranchesEnCoursModeles|null $idModele
     *
     * @return TranchesEnCoursContributeurs
     */
    public function setIdModele(\Edgecreator\Models\TranchesEnCoursModeles $idModele = null)
    {
        $this->idModele = $idModele;

        return $this;
    }

    /**
     * Get idModele.
     *
     * @return \Edgecreator\Models\TranchesEnCoursModeles|null
     */
    public function getIdModele()
    {
        return $this->idModele;
    }
}
