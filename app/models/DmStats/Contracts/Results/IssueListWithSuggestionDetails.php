<?php

namespace DmStats\Contracts\Results;


class IssueListWithSuggestionDetails
{
    /** @var SuggestedIssue[] $issues */
    private $issues = [];

    static $authors = [];
    static $storyDetails = [];
    static $publicationTitles = [];

    public function __construct()
    {
    }

    public function addStory($publicationcode, $issuenumber, $storycode, $score) {
        $issueCode = implode(' ', [$publicationcode, $issuenumber]);
        $this->issues
            [$issueCode]
                ['stories']
                    [] = $storycode;
        $this->issues
            [$issueCode]
                ['score'] = $score;
        $this->issues
            [$issueCode]
                ['publicationcode'] = $publicationcode;
    }

    public function getIssues()
    {
        return $this->issues;
    }
}