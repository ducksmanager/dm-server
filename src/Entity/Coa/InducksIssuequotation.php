<?php

namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksIssuequotation
 *
 * @ORM\Table(name="inducks_issuequotation", uniqueConstraints={@ORM\UniqueConstraint(name="inducks_issuequotation__uindex_issuecode", columns={"issuecode"})}, indexes={@ORM\Index(name="inducks_issuequotation__index_publication", columns={"publicationcode"})})
 * @ORM\Entity
 */
class InducksIssuequotation
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
     * @ORM\Column(name="publicationcode", type="string", length=15, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     */
    private $issuenumber;

    /**
     * @var float|null
     *
     * @ORM\Column(name="estimationmin", type="float", precision=23, scale=0, nullable=true)
     */
    private $estimationmin;

    /**
     * @var float|null
     *
     * @ORM\Column(name="estimationmax", type="float", precision=23, scale=0, nullable=true)
     */
    private $estimationmax;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="scrapedate", type="datetime", nullable=true)
     */
    private $scrapedate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuecode", type="string", length=28, nullable=true)
     */
    private $issuecode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function setPublicationcode(string $publicationcode): self
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function setIssuenumber(string $issuenumber): self
    {
        $this->issuenumber = $issuenumber;

        return $this;
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

    public function setScrapedate(?\DateTimeInterface $scrapedate): self
    {
        $this->scrapedate = $scrapedate;

        return $this;
    }

    public function getIssuecode(): ?string
    {
        return $this->issuecode;
    }

    public function setIssuecode(?string $issuecode): self
    {
        $this->issuecode = $issuecode;

        return $this;
    }


}
