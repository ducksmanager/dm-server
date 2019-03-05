<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStoryversion
 *
 * @ORM\Table(name="inducks_storyversion", indexes={@ORM\Index(name="fk_inducks_storyversion1", columns={"storycode"}), @ORM\Index(name="fulltext_inducks_storyversion", columns={"appsummary", "plotsummary", "writsummary", "artsummary", "inksummary", "creatorrefsummary", "keywordsummary"})})
 * @ORM\Entity
 */
class InducksStoryversion
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
     * @ORM\Column(name="keywordsummary", type="string", length=4191, nullable=true)
     */
    private $keywordsummary;

    /**
     * @var int|null
     *
     * @ORM\Column(name="estimatedpanels", type="integer", nullable=true)
     */
    private $estimatedpanels;

    public function setStoryversioncode($storyversioncode = null): InducksStoryversion
    {
        $this->storyversioncode = $storyversioncode;

        return $this;
    }

    public function getStoryversioncode(): string
    {
        return $this->storyversioncode;
    }

    public function setStorycode($storycode = null): InducksStoryversion
    {
        $this->storycode = $storycode;

        return $this;
    }

    public function getStorycode(): ?string
    {
        return $this->storycode;
    }

    public function setEntirepages($entirepages = null): InducksStoryversion
    {
        $this->entirepages = $entirepages;

        return $this;
    }

    public function getEntirepages(): ?int
    {
        return $this->entirepages;
    }

    public function setBrokenpagenumerator($brokenpagenumerator = null): InducksStoryversion
    {
        $this->brokenpagenumerator = $brokenpagenumerator;

        return $this;
    }

    public function getBrokenpagenumerator(): ?int
    {
        return $this->brokenpagenumerator;
    }

    public function setBrokenpagedenominator($brokenpagedenominator = null): InducksStoryversion
    {
        $this->brokenpagedenominator = $brokenpagedenominator;

        return $this;
    }

    public function getBrokenpagedenominator(): ?int
    {
        return $this->brokenpagedenominator;
    }

    public function setBrokenpageunspecified($brokenpageunspecified = null): InducksStoryversion
    {
        $this->brokenpageunspecified = $brokenpageunspecified;

        return $this;
    }

    public function getBrokenpageunspecified(): ?string
    {
        return $this->brokenpageunspecified;
    }

    public function setKind($kind = null): InducksStoryversion
    {
        $this->kind = $kind;

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setRowsperpage($rowsperpage = null): InducksStoryversion
    {
        $this->rowsperpage = $rowsperpage;

        return $this;
    }

    public function getRowsperpage(): ?int
    {
        return $this->rowsperpage;
    }

    public function setColumnsperpage($columnsperpage = null): InducksStoryversion
    {
        $this->columnsperpage = $columnsperpage;

        return $this;
    }

    public function getColumnsperpage(): ?int
    {
        return $this->columnsperpage;
    }

    public function setAppisxapp($appisxapp = null): InducksStoryversion
    {
        $this->appisxapp = $appisxapp;

        return $this;
    }

    public function getAppisxapp(): ?string
    {
        return $this->appisxapp;
    }

    public function setWhat($what = null): InducksStoryversion
    {
        $this->what = $what;

        return $this;
    }

    public function getWhat(): ?string
    {
        return $this->what;
    }

    public function setAppsummary($appsummary = null): InducksStoryversion
    {
        $this->appsummary = $appsummary;

        return $this;
    }

    public function getAppsummary(): ?string
    {
        return $this->appsummary;
    }

    public function setPlotsummary($plotsummary = null): InducksStoryversion
    {
        $this->plotsummary = $plotsummary;

        return $this;
    }

    public function getPlotsummary(): ?string
    {
        return $this->plotsummary;
    }

    public function setWritsummary($writsummary = null): InducksStoryversion
    {
        $this->writsummary = $writsummary;

        return $this;
    }

    public function getWritsummary(): ?string
    {
        return $this->writsummary;
    }

    public function setArtsummary($artsummary = null): InducksStoryversion
    {
        $this->artsummary = $artsummary;

        return $this;
    }

    public function getArtsummary(): ?string
    {
        return $this->artsummary;
    }

    public function setInksummary($inksummary = null): InducksStoryversion
    {
        $this->inksummary = $inksummary;

        return $this;
    }

    public function getInksummary(): ?string
    {
        return $this->inksummary;
    }

    public function setCreatorrefsummary($creatorrefsummary = null): InducksStoryversion
    {
        $this->creatorrefsummary = $creatorrefsummary;

        return $this;
    }

    public function getCreatorrefsummary(): ?string
    {
        return $this->creatorrefsummary;
    }

    public function setKeywordsummary($keywordsummary = null): InducksStoryversion
    {
        $this->keywordsummary = $keywordsummary;

        return $this;
    }

    public function getKeywordsummary(): ?string
    {
        return $this->keywordsummary;
    }

    public function setEstimatedpanels($estimatedpanels = null): InducksStoryversion
    {
        $this->estimatedpanels = $estimatedpanels;

        return $this;
    }

    public function getEstimatedpanels(): ?int
    {
        return $this->estimatedpanels;
    }
}
