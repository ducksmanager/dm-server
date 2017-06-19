<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStoryversion
 *
 * @ORM\Table(name="inducks_storyversion", indexes={@ORM\Index(name="inducks_entry_pk_storyversioncode", columns={"storyversioncode"}), @ORM\Index(name="inducks_entry_fk_storycode", columns={"storycode"}), @ORM\Index(name="appsummary", columns={"appsummary", "plotsummary", "writsummary", "artsummary", "inksummary", "creatorrefsummary", "keywordsummary"})})
 * @ORM\Entity
 */
class InducksStoryversion extends \Coa\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="storyversioncode", type="string", length=19, nullable=true)
     */
    private $storyversioncode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=true)
     */
    private $storycode;

    /**
     * @var integer
     *
     * @ORM\Column(name="entirepages", type="integer", nullable=true)
     */
    private $entirepages;

    /**
     * @var integer
     *
     * @ORM\Column(name="brokenpagenumerator", type="integer", nullable=true)
     */
    private $brokenpagenumerator;

    /**
     * @var integer
     *
     * @ORM\Column(name="brokenpagedenominator", type="integer", nullable=true)
     */
    private $brokenpagedenominator;

    /**
     * @var string
     *
     * @ORM\Column(name="brokenpageunspecified", type="string", nullable=true)
     */
    private $brokenpageunspecified;

    /**
     * @var string
     *
     * @ORM\Column(name="kind", type="string", length=1, nullable=true)
     */
    private $kind;

    /**
     * @var integer
     *
     * @ORM\Column(name="rowsperpage", type="integer", nullable=true)
     */
    private $rowsperpage;

    /**
     * @var integer
     *
     * @ORM\Column(name="columnsperpage", type="integer", nullable=true)
     */
    private $columnsperpage;

    /**
     * @var string
     *
     * @ORM\Column(name="appisxapp", type="string", nullable=true)
     */
    private $appisxapp;

    /**
     * @var string
     *
     * @ORM\Column(name="what", type="string", length=1, nullable=true)
     */
    private $what;

    /**
     * @var string
     *
     * @ORM\Column(name="appsummary", type="text", length=65535, nullable=true)
     */
    private $appsummary;

    /**
     * @var string
     *
     * @ORM\Column(name="plotsummary", type="text", length=65535, nullable=true)
     */
    private $plotsummary;

    /**
     * @var string
     *
     * @ORM\Column(name="writsummary", type="text", length=65535, nullable=true)
     */
    private $writsummary;

    /**
     * @var string
     *
     * @ORM\Column(name="artsummary", type="text", length=65535, nullable=true)
     */
    private $artsummary;

    /**
     * @var string
     *
     * @ORM\Column(name="inksummary", type="text", length=65535, nullable=true)
     */
    private $inksummary;

    /**
     * @var string
     *
     * @ORM\Column(name="creatorrefsummary", type="text", length=65535, nullable=true)
     */
    private $creatorrefsummary;

    /**
     * @var string
     *
     * @ORM\Column(name="keywordsummary", type="text", length=65535, nullable=true)
     */
    private $keywordsummary;

    /**
     * @var integer
     *
     * @ORM\Column(name="estimatedpanels", type="integer", nullable=true)
     */
    private $estimatedpanels;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return InducksStoryversion
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoryversioncode()
    {
        return $this->storyversioncode;
    }

    /**
     * @param string $storyversioncode
     * @return InducksStoryversion
     */
    public function setStoryversioncode($storyversioncode)
    {
        $this->storyversioncode = $storyversioncode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }

    /**
     * @param string $storycode
     * @return InducksStoryversion
     */
    public function setStorycode($storycode)
    {
        $this->storycode = $storycode;
        return $this;
    }

    /**
     * @return int
     */
    public function getEntirepages()
    {
        return $this->entirepages;
    }

    /**
     * @param int $entirepages
     * @return InducksStoryversion
     */
    public function setEntirepages($entirepages)
    {
        $this->entirepages = $entirepages;
        return $this;
    }

    /**
     * @return int
     */
    public function getBrokenpagenumerator()
    {
        return $this->brokenpagenumerator;
    }

    /**
     * @param int $brokenpagenumerator
     * @return InducksStoryversion
     */
    public function setBrokenpagenumerator($brokenpagenumerator)
    {
        $this->brokenpagenumerator = $brokenpagenumerator;
        return $this;
    }

    /**
     * @return int
     */
    public function getBrokenpagedenominator()
    {
        return $this->brokenpagedenominator;
    }

    /**
     * @param int $brokenpagedenominator
     * @return InducksStoryversion
     */
    public function setBrokenpagedenominator($brokenpagedenominator)
    {
        $this->brokenpagedenominator = $brokenpagedenominator;
        return $this;
    }

    /**
     * @return string
     */
    public function getBrokenpageunspecified()
    {
        return $this->brokenpageunspecified;
    }

    /**
     * @param string $brokenpageunspecified
     * @return InducksStoryversion
     */
    public function setBrokenpageunspecified($brokenpageunspecified)
    {
        $this->brokenpageunspecified = $brokenpageunspecified;
        return $this;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     * @return InducksStoryversion
     */
    public function setKind($kind)
    {
        $this->kind = $kind;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowsperpage()
    {
        return $this->rowsperpage;
    }

    /**
     * @param int $rowsperpage
     * @return InducksStoryversion
     */
    public function setRowsperpage($rowsperpage)
    {
        $this->rowsperpage = $rowsperpage;
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnsperpage()
    {
        return $this->columnsperpage;
    }

    /**
     * @param int $columnsperpage
     * @return InducksStoryversion
     */
    public function setColumnsperpage($columnsperpage)
    {
        $this->columnsperpage = $columnsperpage;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppisxapp()
    {
        return $this->appisxapp;
    }

    /**
     * @param string $appisxapp
     * @return InducksStoryversion
     */
    public function setAppisxapp($appisxapp)
    {
        $this->appisxapp = $appisxapp;
        return $this;
    }

    /**
     * @return string
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * @param string $what
     * @return InducksStoryversion
     */
    public function setWhat($what)
    {
        $this->what = $what;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppsummary()
    {
        return $this->appsummary;
    }

    /**
     * @param string $appsummary
     * @return InducksStoryversion
     */
    public function setAppsummary($appsummary)
    {
        $this->appsummary = $appsummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlotsummary()
    {
        return $this->plotsummary;
    }

    /**
     * @param string $plotsummary
     * @return InducksStoryversion
     */
    public function setPlotsummary($plotsummary)
    {
        $this->plotsummary = $plotsummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getWritsummary()
    {
        return $this->writsummary;
    }

    /**
     * @param string $writsummary
     * @return InducksStoryversion
     */
    public function setWritsummary($writsummary)
    {
        $this->writsummary = $writsummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getArtsummary()
    {
        return $this->artsummary;
    }

    /**
     * @param string $artsummary
     * @return InducksStoryversion
     */
    public function setArtsummary($artsummary)
    {
        $this->artsummary = $artsummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getInksummary()
    {
        return $this->inksummary;
    }

    /**
     * @param string $inksummary
     * @return InducksStoryversion
     */
    public function setInksummary($inksummary)
    {
        $this->inksummary = $inksummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatorrefsummary()
    {
        return $this->creatorrefsummary;
    }

    /**
     * @param string $creatorrefsummary
     * @return InducksStoryversion
     */
    public function setCreatorrefsummary($creatorrefsummary)
    {
        $this->creatorrefsummary = $creatorrefsummary;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywordsummary()
    {
        return $this->keywordsummary;
    }

    /**
     * @param string $keywordsummary
     * @return InducksStoryversion
     */
    public function setKeywordsummary($keywordsummary)
    {
        $this->keywordsummary = $keywordsummary;
        return $this;
    }

    /**
     * @return int
     */
    public function getEstimatedpanels()
    {
        return $this->estimatedpanels;
    }

    /**
     * @param int $estimatedpanels
     * @return InducksStoryversion
     */
    public function setEstimatedpanels($estimatedpanels)
    {
        $this->estimatedpanels = $estimatedpanels;
        return $this;
    }
}

