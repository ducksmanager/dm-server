<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesContributions
 *
 * @ORM\Table(name="tranches_pretes_contributions", indexes={@ORM\Index(name="tranches_pretes_contributions_ID_user_contribution_index", columns={"ID_user", "contribution"})})
 * @ORM\Entity
 */
class TranchesPretesContributions
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
     * @ORM\Column(name="ID_tranche", type="integer", nullable=false)
     */
    private $idTranche;

    /**
     * @var int
     *
     * @ORM\Column(name="ID_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateajout", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTranche(): ?int
    {
        return $this->idTranche;
    }

    public function setIdTranche(int $idTranche): self
    {
        $this->idTranche = $idTranche;

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

    public function getDateajout(): ?\DateTimeInterface
    {
        return $this->dateajout;
    }

    public function setDateajout(\DateTimeInterface $dateajout): self
    {
        $this->dateajout = $dateajout;

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


}
