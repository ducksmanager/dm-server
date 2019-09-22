<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersSuggestionsNotifications
 *
 * @ORM\Table(name="users_suggestions_notifications", uniqueConstraints={@ORM\UniqueConstraint(name="users_notifications__index_user_issue", columns={"User_ID", "issuecode"})})
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
     * @var int
     *
     * @ORM\Column(name="User_ID", type="integer", nullable=false)
     */
    private $userId;

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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

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
