<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksIssue
 *
 * @ORM\Table(name="inducks_issue", indexes={@ORM\Index(name="inducks_issue_fk0", columns={"issuerangecode"}), @ORM\Index(name="inducks_issue_fk1", columns={"publicationcode"})})
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
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="issuerangecode", type="string", length=14, nullable=true)
     */
    private $issuerangecode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=true)
     */
    private $issuenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=158, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=61, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="pages", type="string", length=82, nullable=true)
     */
    private $pages;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="string", length=91, nullable=true)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="printrun", type="string", length=142, nullable=true)
     */
    private $printrun;

    /**
     * @var string
     *
     * @ORM\Column(name="attached", type="string", length=288, nullable=true)
     */
    private $attached;

    /**
     * @var string
     *
     * @ORM\Column(name="oldestdate", type="string", length=10, nullable=true)
     */
    private $oldestdate;

    /**
     * @var string
     *
     * @ORM\Column(name="fullyindexed", type="string", nullable=true)
     */
    private $fullyindexed;

    /**
     * @var string
     *
     * @ORM\Column(name="issuecomment", type="string", length=1270, nullable=true)
     */
    private $issuecomment;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="filledoldestdate", type="string", length=10, nullable=true)
     */
    private $filledoldestdate;

    /**
     * @var string
     *
     * @ORM\Column(name="locked", type="string", nullable=true)
     */
    private $locked;

    /**
     * @var string
     *
     * @ORM\Column(name="inxforbidden", type="string", nullable=true)
     */
    private $inxforbidden;

    /**
     * @var integer
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=8, nullable=true)
     */
    private $maintenanceteamcode;

    /**
     * @param string $publicationcode
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;
    }

    /**
     * Get publicationcode
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuecode
     *
     * @param string $issuecode
     *
     * @return InducksIssue
     */
    public function setIssuecode($issuecode)
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    /**
     * Get issuecode
     *
     * @return string
     */
    public function getIssuecode()
    {
        return $this->issuecode;
    }

    /**
     * Set issuerangecode
     *
     * @param string $issuerangecode
     *
     * @return InducksIssue
     */
    public function setIssuerangecode($issuerangecode)
    {
        $this->issuerangecode = $issuerangecode;

        return $this;
    }

    /**
     * Get issuerangecode
     *
     * @return string
     */
    public function getIssuerangecode()
    {
        return $this->issuerangecode;
    }

    /**
     * Set issuenumber
     *
     * @param string $issuenumber
     *
     * @return InducksIssue
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return InducksIssue
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return InducksIssue
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set pages
     *
     * @param string $pages
     *
     * @return InducksIssue
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return string
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return InducksIssue
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set printrun
     *
     * @param string $printrun
     *
     * @return InducksIssue
     */
    public function setPrintrun($printrun)
    {
        $this->printrun = $printrun;

        return $this;
    }

    /**
     * Get printrun
     *
     * @return string
     */
    public function getPrintrun()
    {
        return $this->printrun;
    }

    /**
     * Set attached
     *
     * @param string $attached
     *
     * @return InducksIssue
     */
    public function setAttached($attached)
    {
        $this->attached = $attached;

        return $this;
    }

    /**
     * Get attached
     *
     * @return string
     */
    public function getAttached()
    {
        return $this->attached;
    }

    /**
     * Set oldestdate
     *
     * @param string $oldestdate
     *
     * @return InducksIssue
     */
    public function setOldestdate($oldestdate)
    {
        $this->oldestdate = $oldestdate;

        return $this;
    }

    /**
     * Get oldestdate
     *
     * @return string
     */
    public function getOldestdate()
    {
        return $this->oldestdate;
    }

    /**
     * Set fullyindexed
     *
     * @param string $fullyindexed
     *
     * @return InducksIssue
     */
    public function setFullyindexed($fullyindexed)
    {
        $this->fullyindexed = $fullyindexed;

        return $this;
    }

    /**
     * Get fullyindexed
     *
     * @return string
     */
    public function getFullyindexed()
    {
        return $this->fullyindexed;
    }

    /**
     * Set issuecomment
     *
     * @param string $issuecomment
     *
     * @return InducksIssue
     */
    public function setIssuecomment($issuecomment)
    {
        $this->issuecomment = $issuecomment;

        return $this;
    }

    /**
     * Get issuecomment
     *
     * @return string
     */
    public function getIssuecomment()
    {
        return $this->issuecomment;
    }

    /**
     * Set error
     *
     * @param string $error
     *
     * @return InducksIssue
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set filledoldestdate
     *
     * @param string $filledoldestdate
     *
     * @return InducksIssue
     */
    public function setFilledoldestdate($filledoldestdate)
    {
        $this->filledoldestdate = $filledoldestdate;

        return $this;
    }

    /**
     * Get filledoldestdate
     *
     * @return string
     */
    public function getFilledoldestdate()
    {
        return $this->filledoldestdate;
    }

    /**
     * Set locked
     *
     * @param string $locked
     *
     * @return InducksIssue
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set inxforbidden
     *
     * @param string $inxforbidden
     *
     * @return InducksIssue
     */
    public function setInxforbidden($inxforbidden)
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    /**
     * Get inxforbidden
     *
     * @return string
     */
    public function getInxforbidden()
    {
        return $this->inxforbidden;
    }

    /**
     * Set inputfilecode
     *
     * @param integer $inputfilecode
     *
     * @return InducksIssue
     */
    public function setInputfilecode($inputfilecode)
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    /**
     * Get inputfilecode
     *
     * @return integer
     */
    public function getInputfilecode()
    {
        return $this->inputfilecode;
    }

    /**
     * Set maintenanceteamcode
     *
     * @param string $maintenanceteamcode
     *
     * @return InducksIssue
     */
    public function setMaintenanceteamcode($maintenanceteamcode)
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    /**
     * Get maintenanceteamcode
     *
     * @return string
     */
    public function getMaintenanceteamcode()
    {
        return $this->maintenanceteamcode;
    }
}
