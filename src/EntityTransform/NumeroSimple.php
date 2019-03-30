<?php

namespace App\EntityTransform;


class NumeroSimple
{
    private $issueNumber;
    private $condition;
    private $purchaseId;

    public function __construct(string $issueNumber, string $condition, ?int $purchaseId)
    {
        $this->issueNumber = $issueNumber;
        $this->condition = $condition;
        $this->purchaseId = $purchaseId;
    }

    public function getIssueNumber() : string
    {
        return $this->issueNumber;
    }

    public function setIssueNumber(string $issueNumber): void
    {
        $this->issueNumber = $issueNumber;
    }

    public function getCondition() : string
    {
        return $this->condition;
    }

    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    public function getPurchaseId() : ?int
    {
        return $this->purchaseId;
    }

    public function setPurchaseId(int $purchaseId): void
    {
        $this->purchaseId = $purchaseId;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}