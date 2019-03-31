<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksIssue
 *
 * @ORM\Table(name="inducks_issue", indexes={@ORM\Index(name="fk_inducks_issue0", columns={"issuerangecode"}), @ORM\Index(name="fk_inducks_issue1", columns={"publicationcode"})})
 * @ORM\Entity
 */
class InducksIssue
{
    /**
     * @var string
     *
     * @ORM\Column(name="issuecode", type="string", length=17, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuerangecode", type="string", length=15, nullable=true)
     */
    private $issuerangecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=true)
     */
    private $publicationcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=true)
     */
    private $issuenumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=158, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="size", type="string", length=61, nullable=true)
     */
    private $size;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pages", type="string", length=82, nullable=true)
     */
    private $pages;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="string", length=160, nullable=true)
     */
    private $price;

    /**
     * @var string|null
     *
     * @ORM\Column(name="printrun", type="string", length=142, nullable=true)
     */
    private $printrun;

    /**
     * @var string|null
     *
     * @ORM\Column(name="attached", type="string", length=288, nullable=true)
     */
    private $attached;

    /**
     * @var string|null
     *
     * @ORM\Column(name="oldestdate", type="string", length=10, nullable=true)
     */
    private $oldestdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fullyindexed", type="string", length=0, nullable=true)
     */
    private $fullyindexed;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuecomment", type="string", length=1270, nullable=true)
     */
    private $issuecomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=0, nullable=true)
     */
    private $error;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filledoldestdate", type="string", length=10, nullable=true)
     */
    private $filledoldestdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locked", type="string", length=0, nullable=true)
     */
    private $locked;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inxforbidden", type="string", length=0, nullable=true)
     */
    private $inxforbidden;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=8, nullable=true)
     */
    private $maintenanceteamcode;

    public function setIssuecode($issuecode = null): InducksIssue
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    public function getIssuecode(): string
    {
        return $this->issuecode;
    }

    public function setIssuerangecode($issuerangecode = null): InducksIssue
    {
        $this->issuerangecode = $issuerangecode;

        return $this;
    }

    public function getIssuerangecode(): ?string
    {
        return $this->issuerangecode;
    }

    public function setPublicationcode($publicationcode = null): InducksIssue
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function setIssuenumber($issuenumber = null): InducksIssue
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function setTitle($title = null): InducksIssue
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSize($size = null): InducksIssue
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setPages($pages = null): InducksIssue
    {
        $this->pages = $pages;

        return $this;
    }

    public function getPages(): ?string
    {
        return $this->pages;
    }

    public function setPrice($price = null): InducksIssue
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrintrun($printrun = null): InducksIssue
    {
        $this->printrun = $printrun;

        return $this;
    }

    public function getPrintrun(): ?string
    {
        return $this->printrun;
    }

    public function setAttached($attached = null): InducksIssue
    {
        $this->attached = $attached;

        return $this;
    }

    public function getAttached(): ?string
    {
        return $this->attached;
    }

    public function setOldestdate($oldestdate = null): InducksIssue
    {
        $this->oldestdate = $oldestdate;

        return $this;
    }

    public function getOldestdate(): ?string
    {
        return $this->oldestdate;
    }

    public function setFullyindexed($fullyindexed = null): InducksIssue
    {
        $this->fullyindexed = $fullyindexed;

        return $this;
    }

    public function getFullyindexed(): ?string
    {
        return $this->fullyindexed;
    }

    public function setIssuecomment($issuecomment = null): InducksIssue
    {
        $this->issuecomment = $issuecomment;

        return $this;
    }

    public function getIssuecomment(): ?string
    {
        return $this->issuecomment;
    }

    public function setError($error = null): InducksIssue
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setFilledoldestdate($filledoldestdate = null): InducksIssue
    {
        $this->filledoldestdate = $filledoldestdate;

        return $this;
    }

    public function getFilledoldestdate(): ?string
    {
        return $this->filledoldestdate;
    }

    public function setLocked($locked = null): InducksIssue
    {
        $this->locked = $locked;

        return $this;
    }

    public function getLocked(): ?string
    {
        return $this->locked;
    }

    public function setInxforbidden($inxforbidden = null): InducksIssue
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    public function getInxforbidden(): ?string
    {
        return $this->inxforbidden;
    }

    public function setInputfilecode($inputfilecode = null): InducksIssue
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    public function getInputfilecode(): ?int
    {
        return $this->inputfilecode;
    }

    public function setMaintenanceteamcode($maintenanceteamcode = null): InducksIssue
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    public function getMaintenanceteamcode(): ?string
    {
        return $this->maintenanceteamcode;
    }
}
