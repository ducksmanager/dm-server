<?php

namespace DmStats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UtilisateursPublicationsSuggerees
 *
 * @ORM\Table(name="utilisateurs_publications_suggerees", indexes={@ORM\Index(name="user", columns={"ID_User"})})
 * @ORM\Entity
 */
class UtilisateursPublicationsSuggerees extends \DmStats\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="Score", type="integer", nullable=false)
     */
    private $score;



    /**
     * Set publicationcode
     *
     * @param string $publicationcode
     *
     * @return UtilisateursPublicationsSuggerees
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
     * @return UtilisateursPublicationsSuggerees
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
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return UtilisateursPublicationsSuggerees
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
     * Set score
     *
     * @param integer $score
     *
     * @return UtilisateursPublicationsSuggerees
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }
}
