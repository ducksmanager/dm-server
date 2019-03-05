<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPoints
 *
 * @ORM\Table(name="users_points")
 * @ORM\Entity
 */
class UsersPoints
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
     * @ORM\Column(name="TypeContribution", type="string", length=0, nullable=false)
     */
    private $typecontribution;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NbPoints", type="integer", nullable=true)
     */
    private $nbpoints = '0';

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

    public function getTypecontribution(): ?string
    {
        return $this->typecontribution;
    }

    public function setTypecontribution(string $typecontribution): self
    {
        $this->typecontribution = $typecontribution;

        return $this;
    }

    public function getNbpoints(): ?int
    {
        return $this->nbpoints;
    }

    public function setNbpoints(?int $nbpoints): self
    {
        $this->nbpoints = $nbpoints;

        return $this;
    }


}
