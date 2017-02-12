<?php

namespace DmStats\Contracts\Results;


class IssueListWithSuggestionDetails
{
    /** @var SuggestedIssue[] $issues */
    private $issues = [];

    static $authors = [];
    static $storyDetails = [];

    public function __construct()
    {
    }

    public function addStory($publicationcode, $issuenumber, $personcode, $storycode, $score) {
        $issueCode = implode(' ', [$publicationcode, $issuenumber]);
        $this->issues
            [$issueCode]
                ['stories']
                    [$storycode] = SuggestedIssue::build(
                        $storycode,
                        self::$storyDetails[$storycode]['storycomment'],
                        self::$storyDetails[$storycode]['title'],
                        $personcode,
                        self::$authors[$personcode]
                    )->toArray();
        $this->issues
            [$issueCode]
                ['score'] = $score;
    }

    public function getIssues()
    {
        return $this->issues;
    }
}