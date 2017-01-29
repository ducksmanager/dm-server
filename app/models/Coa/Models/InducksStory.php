<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStory
 *
 * @ORM\Table(name="inducks_story", indexes={@ORM\Index(name="fk_inducks_story0", columns={"originalstoryversioncode"}), @ORM\Index(name="fk_inducks_story1", columns={"firstpublicationdate"}), @ORM\Index(name="fulltext_inducks_story_temp", columns={"title", "repcountrysummary"})})
 * @ORM\Entity
 */
class InducksStory extends \Coa\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $storycode;

    /**
     * @var string
     *
     * @ORM\Column(name="originalstoryversioncode", type="string", length=19, nullable=true)
     */
    private $originalstoryversioncode;

    /**
     * @var string
     *
     * @ORM\Column(name="creationdate", type="string", length=21, nullable=true)
     */
    private $creationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="firstpublicationdate", type="string", length=10, nullable=true)
     */
    private $firstpublicationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="endpublicationdate", type="string", length=10, nullable=true)
     */
    private $endpublicationdate;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="usedifferentcode", type="string", length=20, nullable=true)
     */
    private $usedifferentcode;

    /**
     * @var string
     *
     * @ORM\Column(name="storycomment", type="string", length=664, nullable=true)
     */
    private $storycomment;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="repcountrysummary", type="text", length=65535, nullable=true)
     */
    private $repcountrysummary;

    /**
     * @var integer
     *
     * @ORM\Column(name="storyparts", type="integer", nullable=true)
     */
    private $storyparts;

    /**
     * @var string
     *
     * @ORM\Column(name="locked", type="string", nullable=true)
     */
    private $locked;

    /**
     * @var integer
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuecodeofstoryitem", type="string", length=14, nullable=true)
     */
    private $issuecodeofstoryitem;

    /**
     * @var string
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=8, nullable=true)
     */
    private $maintenanceteamcode;



    /**
     * Get storycode
     *
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }

    /**
     * Set originalstoryversioncode
     *
     * @param string $originalstoryversioncode
     *
     * @return InducksStory
     */
    public function setOriginalstoryversioncode($originalstoryversioncode)
    {
        $this->originalstoryversioncode = $originalstoryversioncode;

        return $this;
    }

    /**
     * Get originalstoryversioncode
     *
     * @return string
     */
    public function getOriginalstoryversioncode()
    {
        return $this->originalstoryversioncode;
    }

    /**
     * Set creationdate
     *
     * @param string $creationdate
     *
     * @return InducksStory
     */
    public function setCreationdate($creationdate)
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    /**
     * Get creationdate
     *
     * @return string
     */
    public function getCreationdate()
    {
        return $this->creationdate;
    }

    /**
     * Set firstpublicationdate
     *
     * @param string $firstpublicationdate
     *
     * @return InducksStory
     */
    public function setFirstpublicationdate($firstpublicationdate)
    {
        $this->firstpublicationdate = $firstpublicationdate;

        return $this;
    }

    /**
     * Get firstpublicationdate
     *
     * @return string
     */
    public function getFirstpublicationdate()
    {
        return $this->firstpublicationdate;
    }

    /**
     * Set endpublicationdate
     *
     * @param string $endpublicationdate
     *
     * @return InducksStory
     */
    public function setEndpublicationdate($endpublicationdate)
    {
        $this->endpublicationdate = $endpublicationdate;

        return $this;
    }

    /**
     * Get endpublicationdate
     *
     * @return string
     */
    public function getEndpublicationdate()
    {
        return $this->endpublicationdate;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return InducksStory
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
     * Set usedifferentcode
     *
     * @param string $usedifferentcode
     *
     * @return InducksStory
     */
    public function setUsedifferentcode($usedifferentcode)
    {
        $this->usedifferentcode = $usedifferentcode;

        return $this;
    }

    /**
     * Get usedifferentcode
     *
     * @return string
     */
    public function getUsedifferentcode()
    {
        return $this->usedifferentcode;
    }

    /**
     * Set storycomment
     *
     * @param string $storycomment
     *
     * @return InducksStory
     */
    public function setStorycomment($storycomment)
    {
        $this->storycomment = $storycomment;

        return $this;
    }

    /**
     * Get storycomment
     *
     * @return string
     */
    public function getStorycomment()
    {
        return $this->storycomment;
    }

    /**
     * Set error
     *
     * @param string $error
     *
     * @return InducksStory
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
     * Set repcountrysummary
     *
     * @param string $repcountrysummary
     *
     * @return InducksStory
     */
    public function setRepcountrysummary($repcountrysummary)
    {
        $this->repcountrysummary = $repcountrysummary;

        return $this;
    }

    /**
     * Get repcountrysummary
     *
     * @return string
     */
    public function getRepcountrysummary()
    {
        return $this->repcountrysummary;
    }

    /**
     * Set storyparts
     *
     * @param integer $storyparts
     *
     * @return InducksStory
     */
    public function setStoryparts($storyparts)
    {
        $this->storyparts = $storyparts;

        return $this;
    }

    /**
     * Get storyparts
     *
     * @return integer
     */
    public function getStoryparts()
    {
        return $this->storyparts;
    }

    /**
     * Set locked
     *
     * @param string $locked
     *
     * @return InducksStory
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
     * Set inputfilecode
     *
     * @param integer $inputfilecode
     *
     * @return InducksStory
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
     * Set issuecodeofstoryitem
     *
     * @param string $issuecodeofstoryitem
     *
     * @return InducksStory
     */
    public function setIssuecodeofstoryitem($issuecodeofstoryitem)
    {
        $this->issuecodeofstoryitem = $issuecodeofstoryitem;

        return $this;
    }

    /**
     * Get issuecodeofstoryitem
     *
     * @return string
     */
    public function getIssuecodeofstoryitem()
    {
        return $this->issuecodeofstoryitem;
    }

    /**
     * Set maintenanceteamcode
     *
     * @param string $maintenanceteamcode
     *
     * @return InducksStory
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
