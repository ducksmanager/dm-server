<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksStory
 *
 * @ORM\Table(name="inducks_story", indexes={@ORM\Index(name="fk_inducks_story0", columns={"originalstoryversioncode"}), @ORM\Index(name="fk_inducks_story1", columns={"firstpublicationdate"}), @ORM\Index(name="fulltext_inducks_story", columns={"title", "repcountrysummary"})})
 * @ORM\Entity
 */
class InducksStory
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
     * @ORM\Column(name="title", type="string", length=222, nullable=true)
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
     * @ORM\Column(name="repcountrysummary", type="string", length=91, nullable=true)
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

    public function setStorycode($storycode = null): InducksStory
    {
        $this->storycode = $storycode;

        return $this;
    }

    public function getStorycode(): string
    {
        return $this->storycode;
    }

    public function setOriginalstoryversioncode($originalstoryversioncode = null): InducksStory
    {
        $this->originalstoryversioncode = $originalstoryversioncode;

        return $this;
    }

    public function getOriginalstoryversioncode(): ?string
    {
        return $this->originalstoryversioncode;
    }

    public function setCreationdate($creationdate = null): InducksStory
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    public function getCreationdate(): ?string
    {
        return $this->creationdate;
    }

    public function setFirstpublicationdate($firstpublicationdate = null): InducksStory
    {
        $this->firstpublicationdate = $firstpublicationdate;

        return $this;
    }

    public function getFirstpublicationdate(): ?string
    {
        return $this->firstpublicationdate;
    }

    public function setEndpublicationdate($endpublicationdate = null): InducksStory
    {
        $this->endpublicationdate = $endpublicationdate;

        return $this;
    }

    public function getEndpublicationdate(): ?string
    {
        return $this->endpublicationdate;
    }

    public function setTitle($title = null): InducksStory
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setUsedifferentcode($usedifferentcode = null): InducksStory
    {
        $this->usedifferentcode = $usedifferentcode;

        return $this;
    }

    public function getUsedifferentcode(): ?string
    {
        return $this->usedifferentcode;
    }

    public function setStorycomment($storycomment = null): InducksStory
    {
        $this->storycomment = $storycomment;

        return $this;
    }

    public function getStorycomment(): ?string
    {
        return $this->storycomment;
    }

    public function setError($error = null): InducksStory
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setRepcountrysummary($repcountrysummary = null): InducksStory
    {
        $this->repcountrysummary = $repcountrysummary;

        return $this;
    }

    public function getRepcountrysummary(): ?string
    {
        return $this->repcountrysummary;
    }

    public function setStoryparts($storyparts = null): InducksStory
    {
        $this->storyparts = $storyparts;

        return $this;
    }

    public function getStoryparts(): ?int
    {
        return $this->storyparts;
    }

    public function setLocked($locked = null): InducksStory
    {
        $this->locked = $locked;

        return $this;
    }

    public function getLocked(): ?string
    {
        return $this->locked;
    }

    public function setInputfilecode($inputfilecode = null): InducksStory
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    public function getInputfilecode(): ?int
    {
        return $this->inputfilecode;
    }

    public function setIssuecodeofstoryitem($issuecodeofstoryitem = null): InducksStory
    {
        $this->issuecodeofstoryitem = $issuecodeofstoryitem;

        return $this;
    }

    public function getIssuecodeofstoryitem(): ?string
    {
        return $this->issuecodeofstoryitem;
    }

    public function setMaintenanceteamcode($maintenanceteamcode = null): InducksStory
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    public function getMaintenanceteamcode(): ?string
    {
        return $this->maintenanceteamcode;
    }
}
