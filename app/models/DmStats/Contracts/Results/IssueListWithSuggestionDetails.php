<?php

namespace DmStats\Contracts\Results;


class IssueListWithSuggestionDetails
{
    /** @var SuggestedIssue[] $issues */
    private $issues = [];

    public static $authors = [];
    public static $storyDetails = [];
    public static $publicationTitles = [];

    public function __construct()
    {
    }

    public function addStory($publicationcode, $issuenumber, $storycode, $personcode, $score) {
        $issueCode = implode(' ', [$publicationcode, $issuenumber]);
        $this->issues
            [$issueCode]
                ['stories']
                    [$personcode]
                        [] = $storycode;
        $this->issues
            [$issueCode]
                ['score'] = $score;
        $this->issues
            [$issueCode]
                ['publicationcode'] = $publicationcode;
        $this->issues
            [$issueCode]
                ['issuenumber'] = $issuenumber;
    }

    public function getIssues()
    {
        return $this->issues;
    }
}