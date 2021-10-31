<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numeros
 *
 * @ORM\Table(name="numeros", indexes={@ORM\Index(name="Numero_Utilisateur", columns={"Pays", "Magazine", "Numero", "ID_Utilisateur"}), @ORM\Index(name="Numero_nospace_Utilisateur", columns={"Pays", "Magazine", "Numero_nospace", "ID_Utilisateur"}), @ORM\Index(name="Utilisateur", columns={"ID_Utilisateur"}), @ORM\Index(name="Pays_Magazine_Numero_DateAjout", columns={"DateAjout", "Pays", "Magazine", "Numero"}), @ORM\Index(name="Pays_Magazine_Numero", columns={"Pays", "Magazine", "Numero"})})
 * @ORM\Entity
 */
class Numeros
{
    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     */
    private $magazine;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero_nospace", type="string", length=8, nullable=true)
     */
    private $numeroNospace;

    /**
     * @var string
     *
     * @ORM\Column(name="issuecode", type="string", length=8, nullable=true)
     */
    private $issuecode;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=0, nullable=false, options={"default"="indefini"})
     */
    private $etat = 'indefini';

    /**
     * @var int
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false, options={"default"="-1"})
     */
    private $idAcquisition = '-1';

    /**
     * @var bool
     *
     * @ORM\Column(name="AV", type="boolean", nullable=false, options={"default"="false"})
     */
    private $isToSell = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="abonnement", type="boolean", nullable=false, options={"default"="false"})
     */
    private $abonnement = false;

    /**
     * @var int
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     */
    private $idUtilisateur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAjout", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getMagazine(): ?string
    {
        return $this->magazine;
    }

    public function setMagazine(string $magazine): self
    {
        $this->magazine = $magazine;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNumeroNospace(): ?string
    {
        return $this->numeroNospace;
    }

    public function getIssuecode(): string
    {
        return $this->issuecode;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getIdAcquisition(): ?int
    {
        return $this->idAcquisition;
    }

    public function setIdAcquisition(int $idAcquisition): self
    {
        $this->idAcquisition = $idAcquisition;

        return $this;
    }

    public function getIsToSell(): ?bool
    {
        return $this->isToSell;
    }

    public function setIsToSell(bool $isToSell): self
    {
        $this->isToSell = $isToSell;

        return $this;
    }

    public function getAbonnement(): ?bool
    {
        return $this->abonnement;
    }

    public function setAbonnement(bool $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
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

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(\DateTimeInterface $dateajout): self
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


}
