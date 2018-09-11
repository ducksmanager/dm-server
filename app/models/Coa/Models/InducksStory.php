<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStory
 *
 * @ORM\Table(name="inducks_story", indexes={@ORM\Index(name="fk_inducks_story0", columns={"originalstoryversioncode"}), @ORM\Index(name="fk_inducks_story1", columns={"firstpublicationdate"}), @ORM\Index(name="fulltext_inducks_story", columns={"title", "repcountrysummary"})})
 * @ORM\Entity
 */
class InducksStory extends \Coa\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="originalstoryversioncode", type="string", length=19, nullable=true)
     */
    private $originalstoryversioncode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="creationdate", type="string", length=21, nullable=true)
     */
    private $creationdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstpublicationdate", type="string", length=10, nullable=true)
     */
    private $firstpublicationdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="endpublicationdate", type="string", length=10, nullable=true)
     */
    private $endpublicationdate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=210, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="usedifferentcode", type="string", length=20, nullable=true)
     */
    private $usedifferentcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="storycomment", type="string", length=664, nullable=true)
     */
    private $storycomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=0, nullable=true)
     */
    private $error;

    /**
     * @var string|null
     *
     * @ORM\Column(name="repcountrysummary", type="string", length=88, nullable=true)
     */
    private $repcountrysummary;

    /**
     * @var int|null
     *
     * @ORM\Column(name="storyparts", type="integer", nullable=true)
     */
    private $storyparts;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locked", type="string", length=0, nullable=true)
     */
    private $locked;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="issuecodeofstoryitem", type="string", length=14, nullable=true)
     */
    private $issuecodeofstoryitem;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=8, nullable=true)
     */
    private $maintenanceteamcode;



    /**
     * Set storycode.
     *
     * @param string|null $storycode
     *
     * @return InducksStory
     */
    public function setStorycode($storycode = null)
    {
        $this->storycode = $storycode;

        return $this;
    }

    /**
     * Get storycode.
     *
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }

    /**
     * Set originalstoryversioncode.
     *
     * @param string|null $originalstoryversioncode
     *
     * @return InducksStory
     */
    public function setOriginalstoryversioncode($originalstoryversioncode = null)
    {
        $this->originalstoryversioncode = $originalstoryversioncode;

        return $this;
    }

    /**
     * Get originalstoryversioncode.
     *
     * @return string|null
     */
    public function getOriginalstoryversioncode()
    {
        return $this->originalstoryversioncode;
    }

    /**
     * Set creationdate.
     *
     * @param string|null $creationdate
     *
     * @return InducksStory
     */
    public function setCreationdate($creationdate = null)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate.
     *
     * @return string|null
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set firstpublicationdate.
     *
     * @param string|null $firstpublicationdate
     *
     * @return InducksStory
     */
    public function setFirstpublicationdate($firstpublicationdate = null)
    {
        $this->firstpublicationdate = $firstpublicationdate;

        return $this;
    }

    /**
     * Get firstpublicationdate.
     *
     * @return string|null
     */
    public function getFirstpublicationdate()
    {
        return $this->firstpublicationdate;
    }

    /**
     * Set endpublicationdate.
     *
     * @param string|null $endpublicationdate
     *
     * @return InducksStory
     */
    public function setEndpublicationdate($endpublicationdate = null)
    {
        $this->endpublicationdate = $endpublicationdate;

        return $this;
    }

    /**
     * Get endpublicationdate.
     *
     * @return string|null
     */
    public function getEndpublicationdate()
    {
        return $this->endpublicationdate;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return InducksStory
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
     * Set usedifferentcode.
     *
     * @param string|null $usedifferentcode
     *
     * @return InducksStory
     */
    public function setUsedifferentcode($usedifferentcode = null)
    {
        $this->usedifferentcode = $usedifferentcode;

        return $this;
    }

    /**
     * Get usedifferentcode.
     *
     * @return string|null
     */
    public function getUsedifferentcode()
    {
        return $this->usedifferentcode;
    }

    /**
     * Set storycomment.
     *
     * @param string|null $storycomment
     *
     * @return InducksStory
     */
    public function setStorycomment($storycomment = null)
    {
        $this->storycomment = $storycomment;

        return $this;
    }

    /**
     * Get storycomment.
     *
     * @return string|null
     */
    public function getStorycomment()
    {
        return $this->storycomment;
    }

    /**
     * Set error.
     *
     * @param string|null $error
     *
     * @return InducksStory
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
     * Set repcountrysummary.
     *
     * @param string|null $repcountrysummary
     *
     * @return InducksStory
     */
    public function setRepcountrysummary($repcountrysummary = null)
    {
        $this->repcountrysummary = $repcountrysummary;

        return $this;
    }

    /**
     * Get repcountrysummary.
     *
     * @return string|null
     */
    public function getRepcountrysummary()
    {
        return $this->repcountrysummary;
    }

    /**
     * Set storyparts.
     *
     * @param int|null $storyparts
     *
     * @return InducksStory
     */
    public function setStoryparts($storyparts = null)
    {
        $this->storyparts = $storyparts;

        return $this;
    }

    /**
     * Get storyparts.
     *
     * @return int|null
     */
    public function getStoryparts()
    {
        return $this->storyparts;
    }

    /**
     * Set locked.
     *
     * @param string|null $locked
     *
     * @return InducksStory
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
     * Set inputfilecode.
     *
     * @param int|null $inputfilecode
     *
     * @return InducksStory
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
     * Set issuecodeofstoryitem.
     *
     * @param string|null $issuecodeofstoryitem
     *
     * @return InducksStory
     */
    public function setIssuecodeofstoryitem($issuecodeofstoryitem = null)
    {
        $this->issuecodeofstoryitem = $issuecodeofstoryitem;

        return $this;
    }

    /**
     * Get issuecodeofstoryitem.
     *
     * @return string|null
     */
    public function getIssuecodeofstoryitem()
    {
        return $this->issuecodeofstoryitem;
    }

    /**
     * Set maintenanceteamcode.
     *
     * @param string|null $maintenanceteamcode
     *
     * @return InducksStory
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
