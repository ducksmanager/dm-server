<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntry
 *
 * @ORM\Table(name="inducks_entry", indexes={@ORM\Index(name="fk_inducks_entry0", columns={"issuecode"}), @ORM\Index(name="fk_inducks_entry1", columns={"storyversioncode"}), @ORM\Index(name="fk_inducks_entry2", columns={"languagecode"}), @ORM\Index(name="fk_inducks_entry3", columns={"includedinentrycode"}), @ORM\Index(name="fk_inducks_entry4", columns={"position"}), @ORM\Index(name="entryTitleFullText", columns={"title"})})
 * @ORM\Entity
 */
class InducksEntry
{
    /**
     * @var string
     *
     * @ORM\Column(name="entrycode", type="string", length=22, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $entrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuecode", type="string", length=17, nullable=true)
     */
    private $issuecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="storyversioncode", type="string", length=19, nullable=true)
     */
    private $storyversioncode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="languagecode", type="string", length=7, nullable=true)
     */
    private $languagecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="includedinentrycode", type="string", length=19, nullable=true)
     */
    private $includedinentrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="position", type="string", length=9, nullable=true)
     */
    private $position;

    /**
     * @var string|null
     *
     * @ORM\Column(name="printedcode", type="string", length=88, nullable=true)
     */
    private $printedcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="guessedcode", type="string", length=39, nullable=true)
     */
    private $guessedcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=235, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reallytitle", type="string", length=0, nullable=true)
     */
    private $reallytitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="printedhero", type="string", length=96, nullable=true)
     */
    private $printedhero;

    /**
     * @var string|null
     *
     * @ORM\Column(name="changes", type="string", length=628, nullable=true)
     */
    private $changes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cut", type="string", length=104, nullable=true)
     */
    private $cut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="minorchanges", type="string", length=558, nullable=true)
     */
    private $minorchanges;

    /**
     * @var string|null
     *
     * @ORM\Column(name="missingpanels", type="string", length=2, nullable=true)
     */
    private $missingpanels;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mirrored", type="string", length=0, nullable=true)
     */
    private $mirrored;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sideways", type="string", length=0, nullable=true)
     */
    private $sideways;

    /**
     * @var string|null
     *
     * @ORM\Column(name="startdate", type="string", length=10, nullable=true)
     */
    private $startdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enddate", type="string", length=10, nullable=true)
     */
    private $enddate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="identificationuncertain", type="string", length=0, nullable=true)
     */
    private $identificationuncertain;

    /**
     * @var string|null
     *
     * @ORM\Column(name="alsoreprint", type="string", length=111, nullable=true)
     */
    private $alsoreprint;

    /**
     * @var string|null
     *
     * @ORM\Column(name="part", type="string", length=5, nullable=true)
     */
    private $part;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entrycomment", type="string", length=1715, nullable=true)
     */
    private $entrycomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=0, nullable=true)
     */
    private $error;

    public function setEntrycode($entrycode = null): InducksEntry
    {
        $this->entrycode = $entrycode;

        return $this;
    }

    
    public function getEntrycode(): string
    {
        return $this->entrycode;
    }

    public function setIssuecode($issuecode = null): InducksEntry
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    public function getIssuecode(): ?string
    {
        return $this->issuecode;
    }

    public function setStoryversioncode($storyversioncode = null): InducksEntry
    {
        $this->storyversioncode = $storyversioncode;

        return $this;
    }

    public function getStoryversioncode(): ?string
    {
        return $this->storyversioncode;
    }

    public function setLanguagecode($languagecode = null): InducksEntry
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    public function getLanguagecode(): ?string
    {
        return $this->languagecode;
    }

    public function setIncludedinentrycode($includedinentrycode = null): InducksEntry
    {
        $this->includedinentrycode = $includedinentrycode;

        return $this;
    }

    public function getIncludedinentrycode(): ?string
    {
        return $this->includedinentrycode;
    }

    public function setPosition($position = null): InducksEntry
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPrintedcode($printedcode = null): InducksEntry
    {
        $this->printedcode = $printedcode;

        return $this;
    }

    public function getPrintedcode(): ?string
    {
        return $this->printedcode;
    }

    public function setGuessedcode($guessedcode = null): InducksEntry
    {
        $this->guessedcode = $guessedcode;

        return $this;
    }

    public function getGuessedcode(): ?string
    {
        return $this->guessedcode;
    }

    public function setTitle($title = null): InducksEntry
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setReallytitle($reallytitle = null): InducksEntry
    {
        $this->reallytitle = $reallytitle;

        return $this;
    }

    public function getReallytitle(): ?string
    {
        return $this->reallytitle;
    }

    public function setPrintedhero($printedhero = null): InducksEntry
    {
        $this->printedhero = $printedhero;

        return $this;
    }

    public function getPrintedhero(): ?string
    {
        return $this->printedhero;
    }

    public function setChanges($changes = null): InducksEntry
    {
        $this->changes = $changes;

        return $this;
    }

    public function getChanges(): ?string
    {
        return $this->changes;
    }

    public function setCut($cut = null): InducksEntry
    {
        $this->cut = $cut;

        return $this;
    }

    public function getCut(): ?string
    {
        return $this->cut;
    }

    public function setMinorchanges($minorchanges = null): InducksEntry
    {
        $this->minorchanges = $minorchanges;

        return $this;
    }

    public function getMinorchanges(): ?string
    {
        return $this->minorchanges;
    }

    public function setMissingpanels($missingpanels = null): InducksEntry
    {
        $this->missingpanels = $missingpanels;

        return $this;
    }

    public function getMissingpanels(): ?string
    {
        return $this->missingpanels;
    }

    public function setMirrored($mirrored = null): InducksEntry
    {
        $this->mirrored = $mirrored;

        return $this;
    }

    public function getMirrored(): ?string
    {
        return $this->mirrored;
    }

    public function setSideways($sideways = null): InducksEntry
    {
        $this->sideways = $sideways;

        return $this;
    }

    public function getSideways(): ?string
    {
        return $this->sideways;
    }

    public function setStartdate($startdate = null): InducksEntry
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getStartdate(): ?string
    {
        return $this->startdate;
    }

    public function setEnddate($enddate = null): InducksEntry
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getEnddate(): ?string
    {
        return $this->enddate;
    }

    public function setIdentificationuncertain($identificationuncertain = null): InducksEntry
    {
        $this->identificationuncertain = $identificationuncertain;

        return $this;
    }

    public function getIdentificationuncertain(): ?string
    {
        return $this->identificationuncertain;
    }

    public function setAlsoreprint($alsoreprint = null): InducksEntry
    {
        $this->alsoreprint = $alsoreprint;

        return $this;
    }

    public function getAlsoreprint(): ?string
    {
        return $this->alsoreprint;
    }

    public function setPart($part = null): InducksEntry
    {
        $this->part = $part;

        return $this;
    }

    public function getPart(): ?string
    {
        return $this->part;
    }

    public function setEntrycomment($entrycomment = null): InducksEntry
    {
        $this->entrycomment = $entrycomment;

        return $this;
    }

    public function getEntrycomment(): ?string
    {
        return $this->entrycomment;
    }

    public function setError($error = null): InducksEntry
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
