<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosSimple
 *
 * @ORM\Table(name="numeros_simple", uniqueConstraints={@ORM\UniqueConstraint(name="ID_Utilisateur_2", columns={"ID_Utilisateur", "Publicationcode", "Numero"})}, indexes={@ORM\Index(name="user_issue", columns={"Publicationcode", "Numero"}), @ORM\Index(name="ID_Utilisateur", columns={"ID_Utilisateur"})})
 * @ORM\Entity
 */
class NumerosSimple
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
     * @ORM\Column(name="Publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=12, nullable=false)
     */
    private $numero;

    /**
     * @var \AuteursPseudos
     *
     * @ORM\ManyToOne(targetEntity="AuteursPseudos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Utilisateur", referencedColumnName="ID_User")
     * })
     */
    private $idUtilisateur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function setPublicationcode(string $publicationcode): self
    {
        $this->publicationcode = $publicationcode;

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

    public function getIdUtilisateur(): ?AuteursPseudos
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?AuteursPseudos $idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }


}
