<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksIssue
 *
 * @ORM\Table(name="inducks_issue", indexes={@ORM\Index(name="fk_inducks_issue0", columns={"issuerangecode"}), @ORM\Index(name="fk_inducks_issue1", columns={"publicationcode"})})
 * @ORM\Entity
 */
class InducksIssue extends \Coa\Models\BaseModel
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




    /**
     * Set issuecode.
     *
     * @param string|null $issuecode
     *
     * @return InducksIssue
     */
    public function setIssuecode($issuecode = null)
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    /**
     * Get issuecode.
     *
     * @return string
     */
    public function getIssuecode()
    {
        return $this->issuecode;
    }

    /**
     * Set issuerangecode.
     *
     * @param string|null $issuerangecode
     *
     * @return InducksIssue
     */
    public function setIssuerangecode($issuerangecode = null)
    {
        $this->issuerangecode = $issuerangecode;

        return $this;
    }

    /**
     * Get issuerangecode.
     *
     * @return string|null
     */
    public function getIssuerangecode()
    {
        return $this->issuerangecode;
    }

    /**
     * Set publicationcode.
     *
     * @param string|null $publicationcode
     *
     * @return InducksIssue
     */
    public function setPublicationcode($publicationcode = null)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode.
     *
     * @return string|null
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuenumber.
     *
     * @param string|null $issuenumber
     *
     * @return InducksIssue
     */
    public function setIssuenumber($issuenumber = null)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber.
     *
     * @return string|null
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return InducksIssue
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set size.
     *
     * @param string|null $size
     *
     * @return InducksIssue
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set pages.
     *
     * @param string|null $pages
     *
     * @return InducksIssue
     */
    public function setPages($pages = null)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages.
     *
     * @return string|null
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set price.
     *
     * @param string|null $price
     *
     * @return InducksIssue
     */
    public function setPrice($price = null)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set printrun.
     *
     * @param string|null $printrun
     *
     * @return InducksIssue
     */
    public function setPrintrun($printrun = null)
    {
        $this->printrun = $printrun;

        return $this;
    }

    /**
     * Get printrun.
     *
     * @return string|null
     */
    public function getPrintrun()
    {
        return $this->printrun;
    }

    /**
     * Set attached.
     *
     * @param string|null $attached
     *
     * @return InducksIssue
     */
    public function setAttached($attached = null)
    {
        $this->attached = $attached;

        return $this;
    }

    /**
     * Get attached.
     *
     * @return string|null
     */
    public function getAttached()
    {
        return $this->attached;
    }

    /**
     * Set oldestdate.
     *
     * @param string|null $oldestdate
     *
     * @return InducksIssue
     */
    public function setOldestdate($oldestdate = null)
    {
        $this->oldestdate = $oldestdate;

        return $this;
    }

    /**
     * Get oldestdate.
     *
     * @return string|null
     */
    public function getOldestdate()
    {
        return $this->oldestdate;
    }

    /**
     * Set fullyindexed.
     *
     * @param string|null $fullyindexed
     *
     * @return InducksIssue
     */
    public function setFullyindexed($fullyindexed = null)
    {
        $this->fullyindexed = $fullyindexed;

        return $this;
    }

    /**
     * Get fullyindexed.
     *
     * @return string|null
     */
    public function getFullyindexed()
    {
        return $this->fullyindexed;
    }

    /**
     * Set issuecomment.
     *
     * @param string|null $issuecomment
     *
     * @return InducksIssue
     */
    public function setIssuecomment($issuecomment = null)
    {
        $this->issuecomment = $issuecomment;

        return $this;
    }

    /**
     * Get issuecomment.
     *
     * @return string|null
     */
    public function getIssuecomment()
    {
        return $this->issuecomment;
    }

    /**
     * Set error.
     *
     * @param string|null $error
     *
     * @return InducksIssue
     */
    public function setError($error = null)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error.
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set filledoldestdate.
     *
     * @param string|null $filledoldestdate
     *
     * @return InducksIssue
     */
    public function setFilledoldestdate($filledoldestdate = null)
    {
        $this->filledoldestdate = $filledoldestdate;

        return $this;
    }

    /**
     * Get filledoldestdate.
     *
     * @return string|null
     */
    public function getFilledoldestdate()
    {
        return $this->filledoldestdate;
    }

    /**
     * Set locked.
     *
     * @param string|null $locked
     *
     * @return InducksIssue
     */
    public function setLocked($locked = null)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked.
     *
     * @return string|null
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set inxforbidden.
     *
     * @param string|null $inxforbidden
     *
     * @return InducksIssue
     */
    public function setInxforbidden($inxforbidden = null)
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    /**
     * Get inxforbidden.
     *
     * @return string|null
     */
    public function getInxforbidden()
    {
        return $this->inxforbidden;
    }

    /**
     * Set inputfilecode.
     *
     * @param int|null $inputfilecode
     *
     * @return InducksIssue
     */
    public function setInputfilecode($inputfilecode = null)
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    /**
     * Get inputfilecode.
     *
     * @return int|null
     */
    public function getInputfilecode()
    {
        return $this->inputfilecode;
    }

    /**
     * Set maintenanceteamcode.
     *
     * @param string|null $maintenanceteamcode
     *
     * @return InducksIssue
     */
    public function setMaintenanceteamcode($maintenanceteamcode = null)
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    /**
     * Get maintenanceteamcode.
     *
     * @return string|null
     */
    public function getMaintenanceteamcode()
    {
        return $this->maintenanceteamcode;
    }
}
