<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntry
 *
 * @ORM\Table(name="inducks_entry", indexes={@ORM\Index(name="inducks_entry_pk_entrycode", columns={"entrycode"}), @ORM\Index(name="inducks_entry_fk_issuecode", columns={"issuecode"}), @ORM\Index(name="inducks_entry_fk_storyversioncode", columns={"storyversioncode"}), @ORM\Index(name="inducks_entry_fk_languagecode", columns={"languagecode"}), @ORM\Index(name="inducks_entry_fk_includedinentrycode", columns={"includedinentrycode"}), @ORM\Index(name="inducks_entry_fk_position", columns={"position"}), @ORM\Index(name="entryTitleFullText", columns={"title"})})
 * @ORM\Entity
 */
class InducksEntry extends \Coa\Models\BaseModel
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
     * @ORM\Column(name="entrycode", type="string", length=22, nullable=true)
     */
    private $entrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuecode", type="string", length=17, nullable=true)
     */
    private $issuecode;

    /**
     * @var string
     *
     * @ORM\Column(name="storyversioncode", type="string", length=19, nullable=true)
     */
    private $storyversioncode;

    /**
     * @var string
     *
     * @ORM\Column(name="languagecode", type="string", length=7, nullable=true)
     */
    private $languagecode;

    /**
     * @var string
     *
     * @ORM\Column(name="includedinentrycode", type="string", length=19, nullable=true)
     */
    private $includedinentrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=7, nullable=true)
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="printedcode", type="string", length=88, nullable=true)
     */
    private $printedcode;

    /**
     * @var string
     *
     * @ORM\Column(name="guessedcode", type="string", length=39, nullable=true)
     */
    private $guessedcode;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=235, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="reallytitle", type="string", nullable=true)
     */
    private $reallytitle;

    /**
     * @var string
     *
     * @ORM\Column(name="printedhero", type="string", length=259, nullable=true)
     */
    private $printedhero;

    /**
     * @var string
     *
     * @ORM\Column(name="changes", type="string", length=628, nullable=true)
     */
    private $changes;

    /**
     * @var string
     *
     * @ORM\Column(name="cut", type="string", length=100, nullable=true)
     */
    private $cut;

    /**
     * @var string
     *
     * @ORM\Column(name="minorchanges", type="string", length=558, nullable=true)
     */
    private $minorchanges;

    /**
     * @var string
     *
     * @ORM\Column(name="missingpanels", type="string", length=2, nullable=true)
     */
    private $missingpanels;

    /**
     * @var string
     *
     * @ORM\Column(name="mirrored", type="string", nullable=true)
     */
    private $mirrored;

    /**
     * @var string
     *
     * @ORM\Column(name="sideways", type="string", nullable=true)
     */
    private $sideways;

    /**
     * @var string
     *
     * @ORM\Column(name="startdate", type="string", length=10, nullable=true)
     */
    private $startdate;

    /**
     * @var string
     *
     * @ORM\Column(name="enddate", type="string", length=10, nullable=true)
     */
    private $enddate;

    /**
     * @var string
     *
     * @ORM\Column(name="identificationuncertain", type="string", nullable=true)
     */
    private $identificationuncertain;

    /**
     * @var string
     *
     * @ORM\Column(name="alsoreprint", type="string", length=99, nullable=true)
     */
    private $alsoreprint;

    /**
     * @var string
     *
     * @ORM\Column(name="part", type="string", length=5, nullable=true)
     */
    private $part;

    /**
     * @var string
     *
     * @ORM\Column(name="entrycomment", type="string", length=1715, nullable=true)
     */
    private $entrycomment;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", nullable=true)
     */
    private $error;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return InducksEntry
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntrycode()
    {
        return $this->entrycode;
    }

    /**
     * @param string $entrycode
     * @return InducksEntry
     */
    public function setEntrycode($entrycode)
    {
        $this->entrycode = $entrycode;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssuecode()
    {
        return $this->issuecode;
    }

    /**
     * @param string $issuecode
     * @return InducksEntry
     */
    public function setIssuecode($issuecode)
    {
        $this->issuecode = $issuecode;
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
     * @return InducksEntry
     */
    public function setStoryversioncode($storyversioncode)
    {
        $this->storyversioncode = $storyversioncode;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguagecode()
    {
        return $this->languagecode;
    }

    /**
     * @param string $languagecode
     * @return InducksEntry
     */
    public function setLanguagecode($languagecode)
    {
        $this->languagecode = $languagecode;
        return $this;
    }

    /**
     * @return string
     */
    public function getIncludedinentrycode()
    {
        return $this->includedinentrycode;
    }

    /**
     * @param string $includedinentrycode
     * @return InducksEntry
     */
    public function setIncludedinentrycode($includedinentrycode)
    {
        $this->includedinentrycode = $includedinentrycode;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return InducksEntry
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrintedcode()
    {
        return $this->printedcode;
    }

    /**
     * @param string $printedcode
     * @return InducksEntry
     */
    public function setPrintedcode($printedcode)
    {
        $this->printedcode = $printedcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getGuessedcode()
    {
        return $this->guessedcode;
    }

    /**
     * @param string $guessedcode
     * @return InducksEntry
     */
    public function setGuessedcode($guessedcode)
    {
        $this->guessedcode = $guessedcode;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return InducksEntry
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getReallytitle()
    {
        return $this->reallytitle;
    }

    /**
     * @param string $reallytitle
     * @return InducksEntry
     */
    public function setReallytitle($reallytitle)
    {
        $this->reallytitle = $reallytitle;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrintedhero()
    {
        return $this->printedhero;
    }

    /**
     * @param string $printedhero
     * @return InducksEntry
     */
    public function setPrintedhero($printedhero)
    {
        $this->printedhero = $printedhero;
        return $this;
    }

    /**
     * @return string
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * @param string $changes
     * @return InducksEntry
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;
        return $this;
    }

    /**
     * @return string
     */
    public function getCut()
    {
        return $this->cut;
    }

    /**
     * @param string $cut
     * @return InducksEntry
     */
    public function setCut($cut)
    {
        $this->cut = $cut;
        return $this;
    }

    /**
     * @return string
     */
    public function getMinorchanges()
    {
        return $this->minorchanges;
    }

    /**
     * @param string $minorchanges
     * @return InducksEntry
     */
    public function setMinorchanges($minorchanges)
    {
        $this->minorchanges = $minorchanges;
        return $this;
    }

    /**
     * @return string
     */
    public function getMissingpanels()
    {
        return $this->missingpanels;
    }

    /**
     * @param string $missingpanels
     * @return InducksEntry
     */
    public function setMissingpanels($missingpanels)
    {
        $this->missingpanels = $missingpanels;
        return $this;
    }

    /**
     * @return string
     */
    public function getMirrored()
    {
        return $this->mirrored;
    }

    /**
     * @param string $mirrored
     * @return InducksEntry
     */
    public function setMirrored($mirrored)
    {
        $this->mirrored = $mirrored;
        return $this;
    }

    /**
     * @return string
     */
    public function getSideways()
    {
        return $this->sideways;
    }

    /**
     * @param string $sideways
     * @return InducksEntry
     */
    public function setSideways($sideways)
    {
        $this->sideways = $sideways;
        return $this;
    }

    /**
     * @return string
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * @param string $startdate
     * @return InducksEntry
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * @param string $enddate
     * @return InducksEntry
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationuncertain()
    {
        return $this->identificationuncertain;
    }

    /**
     * @param string $identificationuncertain
     * @return InducksEntry
     */
    public function setIdentificationuncertain($identificationuncertain)
    {
        $this->identificationuncertain = $identificationuncertain;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlsoreprint()
    {
        return $this->alsoreprint;
    }

    /**
     * @param string $alsoreprint
     * @return InducksEntry
     */
    public function setAlsoreprint($alsoreprint)
    {
        $this->alsoreprint = $alsoreprint;
        return $this;
    }

    /**
     * @return string
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * @param string $part
     * @return InducksEntry
     */
    public function setPart($part)
    {
        $this->part = $part;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntrycomment()
    {
        return $this->entrycomment;
    }

    /**
     * @param string $entrycomment
     * @return InducksEntry
     */
    public function setEntrycomment($entrycomment)
    {
        $this->entrycomment = $entrycomment;
        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     * @return InducksEntry
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }
}

