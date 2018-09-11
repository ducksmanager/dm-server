<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStoryversion
 *
 * @ORM\Table(name="inducks_storyversion", indexes={@ORM\Index(name="fk_inducks_storyversion1", columns={"storycode"}), @ORM\Index(name="fulltext_inducks_storyversion", columns={"appsummary", "plotsummary", "writsummary", "artsummary", "inksummary", "creatorrefsummary", "keywordsummary"})})
 * @ORM\Entity
 */
class InducksStoryversion extends \Coa\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="storyversioncode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storyversioncode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=true)
     */
    private $storycode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="entirepages", type="integer", nullable=true)
     */
    private $entirepages;

    /**
     * @var int|null
     *
     * @ORM\Column(name="brokenpagenumerator", type="integer", nullable=true)
     */
    private $brokenpagenumerator;

    /**
     * @var int|null
     *
     * @ORM\Column(name="brokenpagedenominator", type="integer", nullable=true)
     */
    private $brokenpagedenominator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="brokenpageunspecified", type="string", length=0, nullable=true)
     */
    private $brokenpageunspecified;

    /**
     * @var string|null
     *
     * @ORM\Column(name="kind", type="string", length=1, nullable=true)
     */
    private $kind;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rowsperpage", type="integer", nullable=true)
     */
    private $rowsperpage;

    /**
     * @var int|null
     *
     * @ORM\Column(name="columnsperpage", type="integer", nullable=true)
     */
    private $columnsperpage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="appisxapp", type="string", length=0, nullable=true)
     */
    private $appisxapp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="what", type="string", length=1, nullable=true)
     */
    private $what;

    /**
     * @var string|null
     *
     * @ORM\Column(name="appsummary", type="string", length=620, nullable=true)
     */
    private $appsummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plotsummary", type="string", length=271, nullable=true)
     */
    private $plotsummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="writsummary", type="string", length=271, nullable=true)
     */
    private $writsummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="artsummary", type="string", length=338, nullable=true)
     */
    private $artsummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inksummary", type="string", length=338, nullable=true)
     */
    private $inksummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="creatorrefsummary", type="string", length=1671, nullable=true)
     */
    private $creatorrefsummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keywordsummary", type="string", length=4204, nullable=true)
     */
    private $keywordsummary;

    /**
     * @var int|null
     *
     * @ORM\Column(name="estimatedpanels", type="integer", nullable=true)
     */
    private $estimatedpanels;



    /**
     * Set storyversioncode.
     *
     * @param string|null $storyversioncode
     *
     * @return InducksStoryversion
     */
    public function setStoryversioncode($storyversioncode = null)
    {
        $this->storyversioncode = $storyversioncode;

        return $this;
    }

    /**
     * Get storyversioncode.
     *
     * @return string
     */
    public function getStoryversioncode()
    {
        return $this->storyversioncode;
    }

    /**
     * Set storycode.
     *
     * @param string|null $storycode
     *
     * @return InducksStoryversion
     */
    public function setStorycode($storycode = null)
    {
        $this->storycode = $storycode;

        return $this;
    }

    /**
     * Get storycode.
     *
     * @return string|null
     */
    public function getStorycode()
    {
        return $this->storycode;
    }

    /**
     * Set entirepages.
     *
     * @param int|null $entirepages
     *
     * @return InducksStoryversion
     */
    public function setEntirepages($entirepages = null)
    {
        $this->entirepages = $entirepages;

        return $this;
    }

    /**
     * Get entirepages.
     *
     * @return int|null
     */
    public function getEntirepages()
    {
        return $this->entirepages;
    }

    /**
     * Set brokenpagenumerator.
     *
     * @param int|null $brokenpagenumerator
     *
     * @return InducksStoryversion
     */
    public function setBrokenpagenumerator($brokenpagenumerator = null)
    {
        $this->brokenpagenumerator = $brokenpagenumerator;

        return $this;
    }

    /**
     * Get brokenpagenumerator.
     *
     * @return int|null
     */
    public function getBrokenpagenumerator()
    {
        return $this->brokenpagenumerator;
    }

    /**
     * Set brokenpagedenominator.
     *
     * @param int|null $brokenpagedenominator
     *
     * @return InducksStoryversion
     */
    public function setBrokenpagedenominator($brokenpagedenominator = null)
    {
        $this->brokenpagedenominator = $brokenpagedenominator;

        return $this;
    }

    /**
     * Get brokenpagedenominator.
     *
     * @return int|null
     */
    public function getBrokenpagedenominator()
    {
        return $this->brokenpagedenominator;
    }

    /**
     * Set brokenpageunspecified.
     *
     * @param string|null $brokenpageunspecified
     *
     * @return InducksStoryversion
     */
    public function setBrokenpageunspecified($brokenpageunspecified = null)
    {
        $this->brokenpageunspecified = $brokenpageunspecified;

        return $this;
    }

    /**
     * Get brokenpageunspecified.
     *
     * @return string|null
     */
    public function getBrokenpageunspecified()
    {
        return $this->brokenpageunspecified;
    }

    /**
     * Set kind.
     *
     * @param string|null $kind
     *
     * @return InducksStoryversion
     */
    public function setKind($kind = null)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Get kind.
     *
     * @return string|null
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set rowsperpage.
     *
     * @param int|null $rowsperpage
     *
     * @return InducksStoryversion
     */
    public function setRowsperpage($rowsperpage = null)
    {
        $this->rowsperpage = $rowsperpage;

        return $this;
    }

    /**
     * Get rowsperpage.
     *
     * @return int|null
     */
    public function getRowsperpage()
    {
        return $this->rowsperpage;
    }

    /**
     * Set columnsperpage.
     *
     * @param int|null $columnsperpage
     *
     * @return InducksStoryversion
     */
    public function setColumnsperpage($columnsperpage = null)
    {
        $this->columnsperpage = $columnsperpage;

        return $this;
    }

    /**
     * Get columnsperpage.
     *
     * @return int|null
     */
    public function getColumnsperpage()
    {
        return $this->columnsperpage;
    }

    /**
     * Set appisxapp.
     *
     * @param string|null $appisxapp
     *
     * @return InducksStoryversion
     */
    public function setAppisxapp($appisxapp = null)
    {
        $this->appisxapp = $appisxapp;

        return $this;
    }

    /**
     * Get appisxapp.
     *
     * @return string|null
     */
    public function getAppisxapp()
    {
        return $this->appisxapp;
    }

    /**
     * Set what.
     *
     * @param string|null $what
     *
     * @return InducksStoryversion
     */
    public function setWhat($what = null)
    {
        $this->what = $what;

        return $this;
    }

    /**
     * Get what.
     *
     * @return string|null
     */
    public function getWhat()
    {
        return $this->what;
    }

    /**
     * Set appsummary.
     *
     * @param string|null $appsummary
     *
     * @return InducksStoryversion
     */
    public function setAppsummary($appsummary = null)
    {
        $this->appsummary = $appsummary;

        return $this;
    }

    /**
     * Get appsummary.
     *
     * @return string|null
     */
    public function getAppsummary()
    {
        return $this->appsummary;
    }

    /**
     * Set plotsummary.
     *
     * @param string|null $plotsummary
     *
     * @return InducksStoryversion
     */
    public function setPlotsummary($plotsummary = null)
    {
        $this->plotsummary = $plotsummary;

        return $this;
    }

    /**
     * Get plotsummary.
     *
     * @return string|null
     */
    public function getPlotsummary()
    {
        return $this->plotsummary;
    }

    /**
     * Set writsummary.
     *
     * @param string|null $writsummary
     *
     * @return InducksStoryversion
     */
    public function setWritsummary($writsummary = null)
    {
        $this->writsummary = $writsummary;

        return $this;
    }

    /**
     * Get writsummary.
     *
     * @return string|null
     */
    public function getWritsummary()
    {
        return $this->writsummary;
    }

    /**
     * Set artsummary.
     *
     * @param string|null $artsummary
     *
     * @return InducksStoryversion
     */
    public function setArtsummary($artsummary = null)
    {
        $this->artsummary = $artsummary;

        return $this;
    }

    /**
     * Get artsummary.
     *
     * @return string|null
     */
    public function getArtsummary()
    {
        return $this->artsummary;
    }

    /**
     * Set inksummary.
     *
     * @param string|null $inksummary
     *
     * @return InducksStoryversion
     */
    public function setInksummary($inksummary = null)
    {
        $this->inksummary = $inksummary;

        return $this;
    }

    /**
     * Get inksummary.
     *
     * @return string|null
     */
    public function getInksummary()
    {
        return $this->inksummary;
    }

    /**
     * Set creatorrefsummary.
     *
     * @param string|null $creatorrefsummary
     *
     * @return InducksStoryversion
     */
    public function setCreatorrefsummary($creatorrefsummary = null)
    {
        $this->creatorrefsummary = $creatorrefsummary;

        return $this;
    }

    /**
     * Get creatorrefsummary.
     *
     * @return string|null
     */
    public function getCreatorrefsummary()
    {
        return $this->creatorrefsummary;
    }

    /**
     * Set keywordsummary.
     *
     * @param string|null $keywordsummary
     *
     * @return InducksStoryversion
     */
    public function setKeywordsummary($keywordsummary = null)
    {
        $this->keywordsummary = $keywordsummary;

        return $this;
    }

    /**
     * Get keywordsummary.
     *
     * @return string|null
     */
    public function getKeywordsummary()
    {
        return $this->keywordsummary;
    }

    /**
     * Set estimatedpanels.
     *
     * @param int|null $estimatedpanels
     *
     * @return InducksStoryversion
     */
    public function setEstimatedpanels($estimatedpanels = null)
    {
        $this->estimatedpanels = $estimatedpanels;

        return $this;
    }

    /**
     * Get estimatedpanels.
     *
     * @return int|null
     */
    public function getEstimatedpanels()
    {
        return $this->estimatedpanels;
    }
}
