<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPoints
 *
 * @ORM\Table(name="users_points")
 * @ORM\Entity
 * @deprecated
 */
class UsersPoints extends \Dm\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="TypeContribution", type="string", length=0, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $typecontribution;

    /**
     * @var int|null
     *
     * @ORM\Column(name="NbPoints", type="integer", nullable=true)
     */
    private $nbpoints = '0';



    /**
     * Set idUtilisateur.
     *
     * @param int $idUtilisateur
     *
     * @return UsersPoints
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
     * Set typecontribution.
     *
     * @param string $typecontribution
     *
     * @return UsersPoints
     */
    public function setTypecontribution($typecontribution)
    {
        $this->typecontribution = $typecontribution;

        return $this;
    }

    /**
     * Get typecontribution.
     *
     * @return string
     */
    public function getTypecontribution()
    {
        return $this->typecontribution;
    }

    /**
     * Set nbpoints.
     *
     * @param int|null $nbpoints
     *
     * @return UsersPoints
     */
    public function setNbpoints($nbpoints = null)
    {
        $this->nbpoints = $nbpoints;

        return $this;
    }

    /**
     * Get nbpoints.
     *
     * @return int|null
     */
    public function getNbpoints()
    {
        return $this->nbpoints;
    }
}
