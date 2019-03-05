<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursContributeurs
 *
 * @ORM\Table(name="tranches_en_cours_contributeurs", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_en_cours_contributeurs__unique", columns={"ID_Modele", "ID_Utilisateur", "contribution"})}, indexes={@ORM\Index(name="IDX_1D8956AC4A1ED576", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursContributeurs
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
     * @var \TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(int $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getContribution(): ?string
    {
        return $this->contribution;
    }

    public function setContribution(string $contribution): self
    {
        $this->contribution = $contribution;

        return $this;
    }

    public function getIdModele(): ?TranchesEnCoursModeles
    {
        return $this->idModele;
    }

    public function setIdModele(?TranchesEnCoursModeles $idModele): self
    {
        $this->idModele = $idModele;

        return $this;
    }


}
