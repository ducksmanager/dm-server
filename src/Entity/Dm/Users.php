<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity
 */
class Users
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
     * @var bool
     *
     * @ORM\Column(name="AccepterPartage", type="boolean", nullable=false, options={"default"="1"})
     */
    private $accepterpartage = '1';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateInscription", type="date", nullable=false)
     */
    private $dateinscription;

    /**
     * @var string
     *
     * @ORM\Column(name="EMail", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="RecommandationsListeMags", type="boolean", nullable=false, options={"default"="1"})
     */
    private $recommandationslistemags = '1';

    /**
     * @var bool
     *
     * @ORM\Column(name="BetaUser", type="boolean", nullable=false)
     */
    private $betauser = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="AfficherVideo", type="boolean", nullable=false, options={"default"="1"})
     */
    private $affichervideo = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture1", type="string", length=20, nullable=false, options={"default"="bois"})
     */
    private $bibliothequeTexture1 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture1", type="string", length=50, nullable=false, options={"default"="HONDURAS MAHOGANY"})
     */
    private $bibliothequeSousTexture1 = 'HONDURAS MAHOGANY';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture2", type="string", length=20, nullable=false, options={"default"="bois"})
     */
    private $bibliothequeTexture2 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture2", type="string", length=50, nullable=false, options={"default"="KNOTTY PINE"})
     */
    private $bibliothequeSousTexture2 = 'KNOTTY PINE';

    /**
     * @var float
     *
     * @ORM\Column(name="Bibliotheque_Grossissement", type="float", precision=10, scale=0, nullable=false, options={"default"="1.5"})
     */
    private $bibliothequeGrossissement = '1.5';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DernierAcces", type="datetime", nullable=false)
     */
    private $dernieracces;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAccepterpartage(): ?bool
    {
        return $this->accepterpartage;
    }

    public function setAccepterpartage(bool $accepterpartage): self
    {
        $this->accepterpartage = $accepterpartage;

        return $this;
    }

    public function getDateinscription(): ?\DateTime
    {
        return $this->dateinscription;
    }

    public function setDateinscription(\DateTime $dateinscription): self
    {
        $this->dateinscription = $dateinscription;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRecommandationslistemags(): ?bool
    {
        return $this->recommandationslistemags;
    }

    public function setRecommandationslistemags(bool $recommandationslistemags): self
    {
        $this->recommandationslistemags = $recommandationslistemags;

        return $this;
    }

    public function getBetauser(): ?bool
    {
        return $this->betauser;
    }

    public function setBetauser(bool $betauser): self
    {
        $this->betauser = $betauser;

        return $this;
    }

    public function getAffichervideo(): ?bool
    {
        return $this->affichervideo;
    }

    public function setAffichervideo(bool $affichervideo): self
    {
        $this->affichervideo = $affichervideo;

        return $this;
    }

    public function getBibliothequeTexture1(): ?string
    {
        return $this->bibliothequeTexture1;
    }

    public function setBibliothequeTexture1(string $bibliothequeTexture1): self
    {
        $this->bibliothequeTexture1 = $bibliothequeTexture1;

        return $this;
    }

    public function getBibliothequeSousTexture1(): ?string
    {
        return $this->bibliothequeSousTexture1;
    }

    public function setBibliothequeSousTexture1(string $bibliothequeSousTexture1): self
    {
        $this->bibliothequeSousTexture1 = $bibliothequeSousTexture1;

        return $this;
    }

    public function getBibliothequeTexture2(): ?string
    {
        return $this->bibliothequeTexture2;
    }

    public function setBibliothequeTexture2(string $bibliothequeTexture2): self
    {
        $this->bibliothequeTexture2 = $bibliothequeTexture2;

        return $this;
    }

    public function getBibliothequeSousTexture2(): ?string
    {
        return $this->bibliothequeSousTexture2;
    }

    public function setBibliothequeSousTexture2(string $bibliothequeSousTexture2): self
    {
        $this->bibliothequeSousTexture2 = $bibliothequeSousTexture2;

        return $this;
    }

    public function getBibliothequeGrossissement(): ?float
    {
        return $this->bibliothequeGrossissement;
    }

    public function setBibliothequeGrossissement(float $bibliothequeGrossissement): self
    {
        $this->bibliothequeGrossissement = $bibliothequeGrossissement;

        return $this;
    }

    public function getDernieracces(): ?\DateTime
    {
        return $this->dernieracces;
    }

    public function setDernieracces(\DateTime $dernieracces): self
    {
        $this->dernieracces = $dernieracces;

        return $this;
    }


}
