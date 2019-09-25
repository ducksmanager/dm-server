<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersSuggestionsNotifications
 *
 * @ORM\Table(name="users_suggestions_notifications", uniqueConstraints={@ORM\UniqueConstraint(name="users_notifications__index_user_issue", columns={"ID_User", "issuecode"})})
 * @ORM\Entity
 */
class UsersSuggestionsNotifications
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
     * @ORM\Column(name="issuecode", type="string", length=12, nullable=false)
     */
    private $issuecode;

    /**
     * @var bool
     *
     * @ORM\Column(name="notified", type="boolean", nullable=false)
     */
    private $notified;

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

    public function getIssuecode(): ?string
    {
        return $this->issuecode;
    }

    public function setIssuecode(string $issuecode): self
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    public function getNotified(): ?bool
    {
        return $this->notified;
    }

    public function setNotified(bool $notified): self
    {
        $this->notified = $notified;

        return $this;
    }


}
