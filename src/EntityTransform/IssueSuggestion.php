<?php
namespace App\EntityTransform;

use stdClass;

class IssueSuggestion
{
    /** @var string $issuecode */
    private $issuecode;

    /** @var int */
    private $score;

    /** @var string[] */
    private $stories;

    /** @var string $publicationcode */
    private $publicationcode;

    /** @var string $issuenumber */
    private $issuenumber;

    /** @var string $oldestdate */
    private $oldestdate;

    /**
     * @param string $issuecode
     * @param int $score
     * @param string[] $stories
     * @param string $publicationcode
     * @param string $issuenumber
     * @param string $oldestdate
     */
    public function __construct(string $issuecode, int $score, array $stories, string $publicationcode, string $issuenumber, string $oldestdate)
    {
        $this->issuecode = $issuecode;
        $this->score = $score;
        $this->stories = $stories;
        $this->publicationcode = $publicationcode;
        $this->issuenumber = $issuenumber;
        $this->oldestdate = $oldestdate;
    }

    public function addStoryCodeForAuthor(string $personcode, string $storycode) {
        if (!isset($this->stories[$personcode])) {
            $this->stories[$personcode] = [];
        }
        $this->stories[$personcode][] = $storycode;
    }

    public function toSimpleObject(): stdClass
    {
        return (object) [
            'issuecode' => $this->issuecode,
            'score' => $this->score,
            'stories' => $this->stories,
            'publicationcode' => $this->publicationcode,
            'issuenumber' => $this->issuenumber,
            'oldestdate' => $this->oldestdate,
        ];
    }


    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getIssuecode(): string
    {
        return $this->issuecode;
    }

    /**
     * @return string[]
     */
    public function getStories(): array
    {
        return $this->stories;
    }

    /**
     * @return string
     */
    public function getPublicationcode(): string
    {
        return $this->publicationcode;
    }

    /**
     * @return string
     */
    public function getIssuenumber(): string
    {
        return $this->issuenumber;
    }

    /**
     * @return string
     */
    public function getOldestdate(): string
    {
        return $this->oldestdate;
    }
}
