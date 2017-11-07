<?php

namespace DmStats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuteursPseudosSimple
 *
 * @ORM\Table(name="auteurs_pseudos_simple", uniqueConstraints={@ORM\UniqueConstraint(name="auteurs_pseudos_simple_ID_User_NomAuteurAbrege_uindex", columns={"ID_User", "NomAuteurAbrege"})}, indexes={@ORM\Index(name="index_auteur_inducks", columns={"NomAuteurAbrege"})})
 * @ORM\Entity
 */
class AuteursPseudosSimple extends \DmStats\Models\BaseModel
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
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteurAbrege", type="string", length=79, nullable=false)
     */
    private $nomauteurabrege;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Notation", type="boolean", nullable=true)
     */
    private $notation;



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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return AuteursPseudosSimple
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set nomauteurabrege
     *
     * @param string $nomauteurabrege
     *
     * @return AuteursPseudosSimple
     */
    public function setNomauteurabrege($nomauteurabrege)
    {
        $this->nomauteurabrege = $nomauteurabrege;

        return $this;
    }

    /**
     * Get nomauteurabrege
     *
     * @return string
     */
    public function getNomauteurabrege()
    {
        return $this->nomauteurabrege;
    }

    /**
     * Set notation
     *
     * @param boolean $notation
     *
     * @return AuteursPseudosSimple
     */
    public function setNotation($notation)
    {
        $this->notation = $notation;

        return $this;
    }

    /**
     * Get notation
     *
     * @return boolean
     */
    public function getNotation()
    {
        return $this->notation;
    }
}
