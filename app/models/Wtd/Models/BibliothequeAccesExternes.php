<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeAccesExternes
 *
 * @ORM\Table(name="bibliotheque_acces_externes")
 * @ORM\Entity
 */
class BibliothequeAccesExternes extends \Wtd\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="Cle", type="string", length=16, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cle;



    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return BibliothequeAccesExternes
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
     * Set cle
     *
     * @param string $cle
     *
     * @return BibliothequeAccesExternes
     */
    public function setCle($cle)
    {
        $this->cle = $cle;

        return $this;
    }

    /**
     * Get cle
     *
     * @return string
     */
    public function getCle()
    {
        return $this->cle;
    }
}
