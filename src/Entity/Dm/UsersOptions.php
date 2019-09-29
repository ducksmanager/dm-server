<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersOptions
 *
 * @ORM\Table(name="users_options", uniqueConstraints={@ORM\UniqueConstraint(name="users_options__unique", columns={"ID_User", "Option_nom", "Option_valeur"})})
 * @ORM\Entity
 */
class UsersOptions
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
     * @var Users
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_User", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="Option_nom", type="string", length=0, nullable=false)
     */
    private $optionNom;

    /**
     * @var string
     *
     * @ORM\Column(name="Option_valeur", type="text", length=65535, nullable=false)
     */
    private $optionValeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOptionNom(): ?string
    {
        return $this->optionNom;
    }

    public function setOptionNom(string $optionNom): self
    {
        $this->optionNom = $optionNom;

        return $this;
    }

    public function getOptionValeur(): ?string
    {
        return $this->optionValeur;
    }

    public function setOptionValeur(string $optionValeur): self
    {
        $this->optionValeur = $optionValeur;

        return $this;
    }


}
