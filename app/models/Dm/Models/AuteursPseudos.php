<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuteursPseudos
 *
 * @ORM\Table(name="auteurs_pseudos")
 * @ORM\Entity
 */
class AuteursPseudos extends \Dm\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteur", type="string", length=50, nullable=false)
     */
    private $nomauteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteurAbrege", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomauteurabrege;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="NbNonPossedesFrance", type="integer", nullable=false)
     */
    private $nbnonpossedesfrance = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="NbNonPossedesEtranger", type="integer", nullable=false)
     */
    private $nbnonpossedesetranger = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="NbPossedes", type="integer", nullable=false)
     */
    private $nbpossedes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateStat", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $datestat = '0000-00-00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="Notation", type="boolean", nullable=false)
     */
    private $notation = '-1';



    /**
     * Set nomauteur
     *
     * @param string $nomauteur
     *
     * @return AuteursPseudos
     */
    public function setNomauteur($nomauteur)
    {
        $this->nomauteur = $nomauteur;

        return $this;
    }

    /**
     * Get nomauteur
     *
     * @return string
     */
    public function getNomauteur()
    {
        return $this->nomauteur;
    }

    /**
     * Set nomauteurabrege
     *
     * @param string $nomauteurabrege
     *
     * @return AuteursPseudos
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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return AuteursPseudos
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
     * Set nbnonpossedesfrance
     *
     * @param integer $nbnonpossedesfrance
     *
     * @return AuteursPseudos
     */
    public function setNbnonpossedesfrance($nbnonpossedesfrance)
    {
        $this->nbnonpossedesfrance = $nbnonpossedesfrance;

        return $this;
    }

    /**
     * Get nbnonpossedesfrance
     *
     * @return integer
     */
    public function getNbnonpossedesfrance()
    {
        return $this->nbnonpossedesfrance;
    }

    /**
     * Set nbnonpossedesetranger
     *
     * @param integer $nbnonpossedesetranger
     *
     * @return AuteursPseudos
     */
    public function setNbnonpossedesetranger($nbnonpossedesetranger)
    {
        $this->nbnonpossedesetranger = $nbnonpossedesetranger;

        return $this;
    }

    /**
     * Get nbnonpossedesetranger
     *
     * @return integer
     */
    public function getNbnonpossedesetranger()
    {
        return $this->nbnonpossedesetranger;
    }

    /**
     * Set nbpossedes
     *
     * @param integer $nbpossedes
     *
     * @return AuteursPseudos
     */
    public function setNbpossedes($nbpossedes)
    {
        $this->nbpossedes = $nbpossedes;

        return $this;
    }

    /**
     * Get nbpossedes
     *
     * @return integer
     */
    public function getNbpossedes()
    {
        return $this->nbpossedes;
    }

    /**
     * Set datestat
     *
     * @param \DateTime $datestat
     *
     * @return AuteursPseudos
     */
    public function setDatestat($datestat)
    {
        $this->datestat = $datestat;

        return $this;
    }

    /**
     * Get datestat
     *
     * @return \DateTime
     */
    public function getDatestat()
    {
        return $this->datestat;
    }

    /**
     * Set notation
     *
     * @param boolean $notation
     *
     * @return AuteursPseudos
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
