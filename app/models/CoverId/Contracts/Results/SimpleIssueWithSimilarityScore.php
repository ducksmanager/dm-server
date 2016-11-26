<?php

namespace Wtd\models\Coa\Contracts\Results;


class SimpleIssueWithSimilarityScore extends SimpleIssue
{
    private $similarityscore;

    /**
     * SimpleIssue constructor.
     * @param $countrycode
     * @param $publicationtitle
     * @param $issuenumber
     * @param $similarityScore
     */
    public function __construct($countrycode, $publicationtitle, $issuenumber, $similarityScore)
    {
        parent::__construct($countrycode, $publicationtitle, $issuenumber);
        $this->similarityScore = $similarityScore;
    }

    /**
     * @return int
     */
    public function getSimilarityscore()
    {
        return $this->similarityscore;
    }/**
     * @param int $similarityscore
     */
    public function setSimilarityscore($similarityscore)
    {
        $this->similarityscore = $similarityscore;
    }

    public function toArray() {
        return [
            'countrycode' => $this->getCountrycode(),
            'publicationtitle' => $this->getPublicationtitle(),
            'issuenumber' => $this->getIssuenumber(),
            'similarityscore' => $this->getSimilarityscore()
        ];
    }
}