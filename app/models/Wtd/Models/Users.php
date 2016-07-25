<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity
 */
class Users extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=40, nullable=false)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="AccepterPartage", type="boolean", nullable=false)
     */
    private $accepterpartage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateInscription", type="date", nullable=false)
     */
    private $dateinscription = '0000-00-00';

    /**
     * @var string
     *
     * @ORM\Column(name="EMail", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="RecommandationsListeMags", type="boolean", nullable=false)
     */
    private $recommandationslistemags = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="BetaUser", type="boolean", nullable=false)
     */
    private $betauser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="AfficherVideo", type="boolean", nullable=false)
     */
    private $affichervideo = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture1", type="string", length=20, nullable=false)
     */
    private $bibliothequeTexture1 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture1", type="string", length=50, nullable=false)
     */
    private $bibliothequeSousTexture1 = 'HONDURAS MAHOGANY';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture2", type="string", length=20, nullable=false)
     */
    private $bibliothequeTexture2 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture2", type="string", length=50, nullable=false)
     */
    private $bibliothequeSousTexture2 = 'KNOTTY PINE';

    /**
     * @var float
     *
     * @ORM\Column(name="Bibliotheque_Grossissement", type="float", precision=10, scale=0, nullable=false)
     */
    private $bibliothequeGrossissement = '1.5';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DernierAcces", type="datetime", nullable=false)
     */
    private $dernieracces = 'CURRENT_TIMESTAMP';



    /**
     * Set username
     *
     * @param string $username
     *
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

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
     * Set accepterpartage
     *
     * @param boolean $accepterpartage
     *
     * @return Users
     */
    public function setAccepterpartage($accepterpartage)
    {
        $this->accepterpartage = $accepterpartage;

        return $this;
    }

    /**
     * Get accepterpartage
     *
     * @return boolean
     */
    public function getAccepterpartage()
    {
        return $this->accepterpartage;
    }

    /**
     * Set dateinscription
     *
     * @param \DateTime $dateinscription
     *
     * @return Users
     */
    public function setDateinscription($dateinscription)
    {
        $this->dateinscription = $dateinscription;

        return $this;
    }

    /**
     * Get dateinscription
     *
     * @return \DateTime
     */
    public function getDateinscription()
    {
        return $this->dateinscription;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set recommandationslistemags
     *
     * @param boolean $recommandationslistemags
     *
     * @return Users
     */
    public function setRecommandationslistemags($recommandationslistemags)
    {
        $this->recommandationslistemags = $recommandationslistemags;

        return $this;
    }

    /**
     * Get recommandationslistemags
     *
     * @return boolean
     */
    public function getRecommandationslistemags()
    {
        return $this->recommandationslistemags;
    }

    /**
     * Set betauser
     *
     * @param boolean $betauser
     *
     * @return Users
     */
    public function setBetauser($betauser)
    {
        $this->betauser = $betauser;

        return $this;
    }

    /**
     * Get betauser
     *
     * @return boolean
     */
    public function getBetauser()
    {
        return $this->betauser;
    }

    /**
     * Set affichervideo
     *
     * @param boolean $affichervideo
     *
     * @return Users
     */
    public function setAffichervideo($affichervideo)
    {
        $this->affichervideo = $affichervideo;

        return $this;
    }

    /**
     * Get affichervideo
     *
     * @return boolean
     */
    public function getAffichervideo()
    {
        return $this->affichervideo;
    }

    /**
     * Set bibliothequeTexture1
     *
     * @param string $bibliothequeTexture1
     *
     * @return Users
     */
    public function setBibliothequeTexture1($bibliothequeTexture1)
    {
        $this->bibliothequeTexture1 = $bibliothequeTexture1;

        return $this;
    }

    /**
     * Get bibliothequeTexture1
     *
     * @return string
     */
    public function getBibliothequeTexture1()
    {
        return $this->bibliothequeTexture1;
    }

    /**
     * Set bibliothequeSousTexture1
     *
     * @param string $bibliothequeSousTexture1
     *
     * @return Users
     */
    public function setBibliothequeSousTexture1($bibliothequeSousTexture1)
    {
        $this->bibliothequeSousTexture1 = $bibliothequeSousTexture1;

        return $this;
    }

    /**
     * Get bibliothequeSousTexture1
     *
     * @return string
     */
    public function getBibliothequeSousTexture1()
    {
        return $this->bibliothequeSousTexture1;
    }

    /**
     * Set bibliothequeTexture2
     *
     * @param string $bibliothequeTexture2
     *
     * @return Users
     */
    public function setBibliothequeTexture2($bibliothequeTexture2)
    {
        $this->bibliothequeTexture2 = $bibliothequeTexture2;

        return $this;
    }

    /**
     * Get bibliothequeTexture2
     *
     * @return string
     */
    public function getBibliothequeTexture2()
    {
        return $this->bibliothequeTexture2;
    }

    /**
     * Set bibliothequeSousTexture2
     *
     * @param string $bibliothequeSousTexture2
     *
     * @return Users
     */
    public function setBibliothequeSousTexture2($bibliothequeSousTexture2)
    {
        $this->bibliothequeSousTexture2 = $bibliothequeSousTexture2;

        return $this;
    }

    /**
     * Get bibliothequeSousTexture2
     *
     * @return string
     */
    public function getBibliothequeSousTexture2()
    {
        return $this->bibliothequeSousTexture2;
    }

    /**
     * Set bibliothequeGrossissement
     *
     * @param float $bibliothequeGrossissement
     *
     * @return Users
     */
    public function setBibliothequeGrossissement($bibliothequeGrossissement)
    {
        $this->bibliothequeGrossissement = $bibliothequeGrossissement;

        return $this;
    }

    /**
     * Get bibliothequeGrossissement
     *
     * @return float
     */
    public function getBibliothequeGrossissement()
    {
        return $this->bibliothequeGrossissement;
    }

    /**
     * Set dernieracces
     *
     * @param \DateTime $dernieracces
     *
     * @return Users
     */
    public function setDernieracces($dernieracces)
    {
        $this->dernieracces = $dernieracces;

        return $this;
    }

    /**
     * Get dernieracces
     *
     * @return \DateTime
     */
    public function getDernieracces()
    {
        return $this->dernieracces;
    }
}
