<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntry
 *
 * @ORM\Table(name="inducks_entry", indexes={@ORM\Index(name="fk_inducks_entry0", columns={"issuecode"}), @ORM\Index(name="fk_inducks_entry1", columns={"storyversioncode"}), @ORM\Index(name="fk_inducks_entry2", columns={"languagecode"}), @ORM\Index(name="fk_inducks_entry3", columns={"includedinentrycode"}), @ORM\Index(name="fk_inducks_entry4", columns={"position"}), @ORM\Index(name="entryTitleFullText", columns={"title"})})
 * @ORM\Entity
 */
class InducksEntry extends \Coa\Models\BaseModel
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



    /**
     * Set entrycode.
     *
     * @param string|null $entrycode
     *
     * @return InducksEntry
     */
    public function setEntrycode($entrycode = null)
    {
        $this->entrycode = $entrycode;

        return $this;
    }

    /**
     * Get entrycode.
     *
     * @return string
     */
    public function getEntrycode()
    {
        return $this->entrycode;
    }

    /**
     * Set issuecode.
     *
     * @param string|null $issuecode
     *
     * @return InducksEntry
     */
    public function setIssuecode($issuecode = null)
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    /**
     * Get issuecode.
     *
     * @return string|null
     */
    public function getIssuecode()
    {
        return $this->issuecode;
    }

    /**
     * Set storyversioncode.
     *
     * @param string|null $storyversioncode
     *
     * @return InducksEntry
     */
    public function setStoryversioncode($storyversioncode = null)
    {
        $this->storyversioncode = $storyversioncode;

        return $this;
    }

    /**
     * Get storyversioncode.
     *
     * @return string|null
     */
    public function getStoryversioncode()
    {
        return $this->storyversioncode;
    }

    /**
     * Set languagecode.
     *
     * @param string|null $languagecode
     *
     * @return InducksEntry
     */
    public function setLanguagecode($languagecode = null)
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    /**
     * Get languagecode.
     *
     * @return string|null
     */
    public function getLanguagecode()
    {
        return $this->languagecode;
    }

    /**
     * Set includedinentrycode.
     *
     * @param string|null $includedinentrycode
     *
     * @return InducksEntry
     */
    public function setIncludedinentrycode($includedinentrycode = null)
    {
        $this->includedinentrycode = $includedinentrycode;

        return $this;
    }

    /**
     * Get includedinentrycode.
     *
     * @return string|null
     */
    public function getIncludedinentrycode()
    {
        return $this->includedinentrycode;
    }

    /**
     * Set position.
     *
     * @param string|null $position
     *
     * @return InducksEntry
     */
    public function setPosition($position = null)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return string|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set printedcode.
     *
     * @param string|null $printedcode
     *
     * @return InducksEntry
     */
    public function setPrintedcode($printedcode = null)
    {
        $this->printedcode = $printedcode;

        return $this;
    }

    /**
     * Get printedcode.
     *
     * @return string|null
     */
    public function getPrintedcode()
    {
        return $this->printedcode;
    }

    /**
     * Set guessedcode.
     *
     * @param string|null $guessedcode
     *
     * @return InducksEntry
     */
    public function setGuessedcode($guessedcode = null)
    {
        $this->guessedcode = $guessedcode;

        return $this;
    }

    /**
     * Get guessedcode.
     *
     * @return string|null
     */
    public function getGuessedcode()
    {
        return $this->guessedcode;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return InducksEntry
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
     * Set reallytitle.
     *
     * @param string|null $reallytitle
     *
     * @return InducksEntry
     */
    public function setReallytitle($reallytitle = null)
    {
        $this->reallytitle = $reallytitle;

        return $this;
    }

    /**
     * Get reallytitle.
     *
     * @return string|null
     */
    public function getReallytitle()
    {
        return $this->reallytitle;
    }

    /**
     * Set printedhero.
     *
     * @param string|null $printedhero
     *
     * @return InducksEntry
     */
    public function setPrintedhero($printedhero = null)
    {
        $this->printedhero = $printedhero;

        return $this;
    }

    /**
     * Get printedhero.
     *
     * @return string|null
     */
    public function getPrintedhero()
    {
        return $this->printedhero;
    }

    /**
     * Set changes.
     *
     * @param string|null $changes
     *
     * @return InducksEntry
     */
    public function setChanges($changes = null)
    {
        $this->changes = $changes;

        return $this;
    }

    /**
     * Get changes.
     *
     * @return string|null
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Set cut.
     *
     * @param string|null $cut
     *
     * @return InducksEntry
     */
    public function setCut($cut = null)
    {
        $this->cut = $cut;

        return $this;
    }

    /**
     * Get cut.
     *
     * @return string|null
     */
    public function getCut()
    {
        return $this->cut;
    }

    /**
     * Set minorchanges.
     *
     * @param string|null $minorchanges
     *
     * @return InducksEntry
     */
    public function setMinorchanges($minorchanges = null)
    {
        $this->minorchanges = $minorchanges;

        return $this;
    }

    /**
     * Get minorchanges.
     *
     * @return string|null
     */
    public function getMinorchanges()
    {
        return $this->minorchanges;
    }

    /**
     * Set missingpanels.
     *
     * @param string|null $missingpanels
     *
     * @return InducksEntry
     */
    public function setMissingpanels($missingpanels = null)
    {
        $this->missingpanels = $missingpanels;

        return $this;
    }

    /**
     * Get missingpanels.
     *
     * @return string|null
     */
    public function getMissingpanels()
    {
        return $this->missingpanels;
    }

    /**
     * Set mirrored.
     *
     * @param string|null $mirrored
     *
     * @return InducksEntry
     */
    public function setMirrored($mirrored = null)
    {
        $this->mirrored = $mirrored;

        return $this;
    }

    /**
     * Get mirrored.
     *
     * @return string|null
     */
    public function getMirrored()
    {
        return $this->mirrored;
    }

    /**
     * Set sideways.
     *
     * @param string|null $sideways
     *
     * @return InducksEntry
     */
    public function setSideways($sideways = null)
    {
        $this->sideways = $sideways;

        return $this;
    }

    /**
     * Get sideways.
     *
     * @return string|null
     */
    public function getSideways()
    {
        return $this->sideways;
    }

    /**
     * Set startdate.
     *
     * @param string|null $startdate
     *
     * @return InducksEntry
     */
    public function setStartdate($startdate = null)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate.
     *
     * @return string|null
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate.
     *
     * @param string|null $enddate
     *
     * @return InducksEntry
     */
    public function setEnddate($enddate = null)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate.
     *
     * @return string|null
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set identificationuncertain.
     *
     * @param string|null $identificationuncertain
     *
     * @return InducksEntry
     */
    public function setIdentificationuncertain($identificationuncertain = null)
    {
        $this->identificationuncertain = $identificationuncertain;

        return $this;
    }

    /**
     * Get identificationuncertain.
     *
     * @return string|null
     */
    public function getIdentificationuncertain()
    {
        return $this->identificationuncertain;
    }

    /**
     * Set alsoreprint.
     *
     * @param string|null $alsoreprint
     *
     * @return InducksEntry
     */
    public function setAlsoreprint($alsoreprint = null)
    {
        $this->alsoreprint = $alsoreprint;

        return $this;
    }

    /**
     * Get alsoreprint.
     *
     * @return string|null
     */
    public function getAlsoreprint()
    {
        return $this->alsoreprint;
    }

    /**
     * Set part.
     *
     * @param string|null $part
     *
     * @return InducksEntry
     */
    public function setPart($part = null)
    {
        $this->part = $part;

        return $this;
    }

    /**
     * Get part.
     *
     * @return string|null
     */
    public function getPart()
    {
        return $this->part;
    }

    /**
     * Set entrycomment.
     *
     * @param string|null $entrycomment
     *
     * @return InducksEntry
     */
    public function setEntrycomment($entrycomment = null)
    {
        $this->entrycomment = $entrycomment;

        return $this;
    }

    /**
     * Get entrycomment.
     *
     * @return string|null
     */
    public function getEntrycomment()
    {
        return $this->entrycomment;
    }

    /**
     * Set error.
     *
     * @param string|null $error
     *
     * @return InducksEntry
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
}
