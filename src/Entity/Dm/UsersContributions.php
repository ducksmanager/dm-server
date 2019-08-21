<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersContributions
 *
 * @ORM\Table(name="users_contributions", indexes={@ORM\Index(name="users_contributions__user_contribution", columns={"ID_user", "contribution"})})
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
     * @var TranchesPretes
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="TranchesPretes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_tranche", referencedColumnName="ID")
     * })
     */
    private $tranche;

    /**
     * @var Bouquineries
     *
     * @ORM\ManyToOne(fetch="EAGER", targetEntity="Bouquineries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_bookstore", referencedColumnName="ID")
     * })
     */
    private $bookstore;

    /**
     * @var int
     *
     * @ORM\Column(name="ID_user", type="integer", nullable=false)
     */
    private $idUser;

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
     * @var int
     *
     * @ORM\Column(name="emails_sent", type="integer", nullable=false)
     */
    private $emailsSent = '0';

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

    public function setBookstore(?Bouquineries $bookstore): self
    {
        $this->bookstore = $bookstore;

        return $this;
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

    public function getEmailsSent(): ?int
    {
        return $this->emailsSent;
    }

    public function setEmailsSent(int $emailsSent): self
    {
        $this->emailsSent = $emailsSent;

        return $this;
    }


}
