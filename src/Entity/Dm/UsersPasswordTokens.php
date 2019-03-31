<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPasswordTokens
 *
 * @ORM\Table(name="users_password_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="users_password_tokens_unique", columns={"ID_User", "Token"})})
 * @ORM\Entity
 */
class UsersPasswordTokens
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
     * @var int
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="Token", type="string", length=16, nullable=false)
     */
    private $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }


}
