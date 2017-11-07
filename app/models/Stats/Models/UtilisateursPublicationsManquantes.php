<?php

namespace Stats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursPublicationsManquantes
 *
 * @ORM\Table(name="utilisateurs_publications_manquantes", indexes={@ORM\Index(name="issue", columns={"ID_User", "publicationcode", "issuenumber"}), @ORM\Index(name="user_stories", columns={"ID_User", "personcode", "storycode"})})
 * @ORM\Entity
 */
class UtilisateursPublicationsManquantes extends \Stats\Models\BaseModel
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
     * @ORM\Column(name="personcode", type="string", length=22, nullable=false)
     */
    private $personcode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     */
    private $storycode;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     */
    private $issuenumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Notation", type="boolean", nullable=false)
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
     * @return UtilisateursPublicationsManquantes
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
     * Set personcode
     *
     * @param string $personcode
     *
     * @return UtilisateursPublicationsManquantes
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
     * @return UtilisateursPublicationsManquantes
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
     * Set publicationcode
     *
     * @param string $publicationcode
     *
     * @return UtilisateursPublicationsManquantes
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuenumber
     *
     * @param string $issuenumber
     *
     * @return UtilisateursPublicationsManquantes
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set notation
     *
     * @param boolean $notation
     *
     * @return UtilisateursPublicationsManquantes
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
