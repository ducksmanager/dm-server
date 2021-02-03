<?php

namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksIssuequotation
 *
 * @ORM\Table(name="inducks_issuequotation")
 * @ORM\Entity
 */
class InducksIssuequotation
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=15, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="estimationmin", type="float", precision=10, scale=0, nullable=true)
     */
    private $estimationmin;

    /**
     * @var float|null
     *
     * @ORM\Column(name="estimationmax", type="float", precision=10, scale=0, nullable=true)
     */
    private $estimationmax;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="scrapedate", type="datetime", nullable=false)
     */
    private $scrapedate;

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function getEstimationmin(): ?float
    {
        return $this->estimationmin;
    }

    public function setEstimationmin(?float $estimationmin): self
    {
        $this->estimationmin = $estimationmin;

        return $this;
    }

    public function getEstimationmax(): ?float
    {
        return $this->estimationmax;
    }

    public function setEstimationmax(?float $estimationmax): self
    {
        $this->estimationmax = $estimationmax;

        return $this;
    }

    public function getScrapedate(): ?\DateTimeInterface
    {
        return $this->scrapedate;
    }

    public function setScrapedate(\DateTimeInterface $scrapedate): self
    {
        $this->scrapedate = $scrapedate;

        return $this;
    }


}
