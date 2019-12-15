<?php


namespace App\EntityTransform;


class IssueSuggestionList
{
    /** @var IssueSuggestion[] $issues */
    private $issues = [];

    /** @var int $minScore */
    private $minScore = 0;

    /** @var int $maxScore */
    private $maxScore = 0;

    public function getIssueWithCode(string $issueCode) : ?IssueSuggestion {
        return $this->issues[$issueCode] ?? null;
    }

    public function addOrReplaceIssue(IssueSuggestion $issue) {
        $this->issues[$issue->getIssuecode()]=$issue;
        $this->recalculateMinMaxScore();
    }

    public function setIssues(array $issues) {
        $this->issues=$issues;
        $this->recalculateMinMaxScore();

        return $this;
    }

    private function recalculateMinMaxScore(): void
    {
        $scores = array_values(array_map(function (IssueSuggestion $issue) {
            return $issue->getScore();
        }, $this->issues));

        $this->minScore = min($scores);
        $this->maxScore = max($scores);
    }

    /**
     * @return IssueSuggestion[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }

    /**
     * @return int
     */
    public function getMinScore(): int
    {
        return $this->minScore;
    }

    /**
     * @return int
     */
    public function getMaxScore(): int
    {
        return $this->maxScore;
    }


}
