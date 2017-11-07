<?php

namespace Stats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursHistoiresManquantes
 *
 * @ORM\Table(name="utilisateurs_histoires_manquantes")
 * @ORM\Entity
 */
class UtilisateursHistoiresManquantes extends \Stats\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="personcode", type="string", length=22, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $personcode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storycode;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;



    /**
     * Set personcode
     *
     * @param string $personcode
     *
     * @return UtilisateursHistoiresManquantes
     */
    public function setPersoncode($personcode)
    {
        $this->personcode = $personcode;

        return $this;
    }

    /**
     * Get personcode
     *
     * @return string
     */
    public function getPersoncode()
    {
        return $this->personcode;
    }

    /**
     * Set storycode
     *
     * @param string $storycode
     *
     * @return UtilisateursHistoiresManquantes
     */
    public function setStorycode($storycode)
    {
        $this->storycode = $storycode;

        return $this;
    }

    /**
     * Get storycode
     *
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return UtilisateursHistoiresManquantes
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
}
