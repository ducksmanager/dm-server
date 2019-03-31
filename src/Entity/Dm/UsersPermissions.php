<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPermissions
 *
 * @ORM\Table(name="users_permissions", uniqueConstraints={@ORM\UniqueConstraint(name="permission_username_role", columns={"username", "role"})})
 * @ORM\Entity
 */
class UsersPermissions
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
     * @ORM\Column(name="role", type="string", length=20, nullable=false)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="privilege", type="string", length=0, nullable=false)
     */
    private $privilege;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

    public function setPrivilege(string $privilege): self
    {
        $this->privilege = $privilege;

        return $this;
    }


}
