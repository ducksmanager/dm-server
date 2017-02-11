<?php

namespace DmStats\Contracts\Results;


class IssueListWithSuggestionDetails
{
    /** @var SuggestedStory[] $stories */
    private $stories = [];

    static $authors = [];
    static $storyDetails = [];

    public function __construct()
    {
    }

    public function addStory($publicationcode, $issuenumber, $personcode, $storycode, $score) {
        $issueCode = implode(' ', [$publicationcode, $issuenumber]);
        $this->stories
            [$issueCode]
                ['stories']
                    [$storycode] = SuggestedStory::build(
                        $storycode,
                        self::$storyDetails[$storycode]['storycomment'],
                        self::$storyDetails[$storycode]['title'],
                        $personcode,
                        self::$authors[$personcode]
                    )->toArray();
        $this->stories
            [$issueCode]
                ['score'] = $score;
    }

    public function getStories()
    {
        return $this->stories;
    }
}