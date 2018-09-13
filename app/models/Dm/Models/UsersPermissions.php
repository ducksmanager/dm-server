<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersPermissions
 *
 * @ORM\Table(name="users_permissions", uniqueConstraints={@ORM\UniqueConstraint(name="permission_username_role", columns={"username", "role"})})
 * @ORM\Entity
 */
class UsersPermissions extends \Dm\Models\BaseModel
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
     * Set username.
     *
     * @param string $username
     *
     * @return UsersPermissions
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set role.
     *
     * @param string $role
     *
     * @return UsersPermissions
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set privilege.
     *
     * @param string $privilege
     *
     * @return UsersPermissions
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * Get privilege.
     *
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }
}
