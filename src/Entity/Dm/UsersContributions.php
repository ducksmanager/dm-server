<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersContributions
 *
 * @ORM\Table(name="users_contributions", indexes={@ORM\Index(name="users_contributions__user_contribution", columns={"ID_user", "contribution"}), @ORM\Index(name="IDX_7FDC16F3CEA2F6E1", columns={"ID_user"})})
 * @ORM\Entity
 */
class UsersContributions
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
     * @var TranchesPretes|null
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="TranchesPretes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_tranche", referencedColumnName="ID")
     * })
     */
    private $tranche;

    /**
     * @var Bouquineries|null
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Bouquineries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_bookstore", referencedColumnName="ID")
     * })
     */
    private $bookstore;

    /**
     * @var BouquineriesCommentaires|null
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="BouquineriesCommentaires")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_bookstore_comment", referencedColumnName="ID")
     * })
     */
    private $bookstoreComment;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_user", referencedColumnName="ID")
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $date = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="contribution", type="string", length=0, nullable=false)
     */
    private $contribution;

    /**
     * @var int
     *
     * @ORM\Column(name="points_new", type="integer", nullable=false)
     */
    private $pointsNew;

    /**
     * @var int
     *
     * @ORM\Column(name="points_total", type="integer", nullable=false)
     */
    private $pointsTotal;

    /**
     * @var bool
     *
     * @ORM\Column(name="emails_sent", type="boolean", nullable=false)
     */
    private $emailsSent = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranche(): ?TranchesPretes
    {
        return $this->tranche;
    }

    public function setTranche(?TranchesPretes $tranche): self
    {
        $this->tranche = $tranche;

        return $this;
    }

    public function getBookstore(): ?Bouquineries
    {
        return $this->bookstore;
    }

    public function setBookstoreComment(?BouquineriesCommentaires $bookstoreComment): self
    {
        $this->bookstoreComment = $bookstoreComment;

        return $this;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContribution(): ?string
    {
        return $this->contribution;
    }

    public function setContribution(string $contribution): self
    {
        $this->contribution = $contribution;

        return $this;
    }

    public function getPointsNew(): ?int
    {
        return $this->pointsNew;
    }

    public function setPointsNew(int $pointsNew): self
    {
        $this->pointsNew = $pointsNew;

        return $this;
    }

    public function getPointsTotal(): ?int
    {
        return $this->pointsTotal;
    }

    public function setPointsTotal(int $pointsTotal): self
    {
        $this->pointsTotal = $pointsTotal;

        return $this;
    }

    public function getEmailsSent(): ?bool
    {
        return $this->emailsSent;
    }

    public function setEmailsSent(bool $emailsSent): self
    {
        $this->emailsSent = $emailsSent;

        return $this;
    }


}
