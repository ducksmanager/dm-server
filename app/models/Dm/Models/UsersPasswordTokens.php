<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPasswordTokens
 *
 * @ORM\Table(name="users_password_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="users_password_tokens_unique", columns={"ID_User", "Token"})})
 * @ORM\Entity
 */
class UsersPasswordTokens extends \Dm\Models\BaseModel
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



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idUser.
     *
     * @param int $idUser
     *
     * @return UsersPasswordTokens
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser.
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return UsersPasswordTokens
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
