<?php

namespace App\EntityTransform;

use App\Helper\GenericReturnObjectInterface;

class IssueWithCoverIdAndPopularity implements GenericReturnObjectInterface
{
    private string $countrycode;
    private string $publicationcode;
    private string $publicationtitle;
    private string $issuenumber;
    private int $coverid;
    private string $coverurl;
    private ?array $quotation = null;
    private int $popularity = 0;

    public static function buildWithoutCoverId($countrycode, $publicationcode, $publicationtitle, $issuenumber): IssueWithCoverIdAndPopularity
    {
        $o = new self();
        $o->countrycode = $countrycode;
        $o->publicationcode = $publicationcode;
        $o->publicationtitle = $publicationtitle;
        $o->issuenumber = $issuenumber;

        return $o;
    }

    public function setCountrycode(string $countrycode)
    {
        $this->countrycode = $countrycode;
    }

    public function setPublicationcode(string $publicationcode)
    {
        $this->publicationcode = $publicationcode;
    }

    public function setPublicationtitle(string $publicationtitle)
    {
        $this->publicationtitle = $publicationtitle;
    }

    public function setIssuenumber(string $issuenumber)
    {
        $this->issuenumber = $issuenumber;
    }

    public function setCoverid(int $coverid)
    {
        $this->coverid = $coverid;
    }

    public function setCoverurl(string $coverurl): void
    {
        $this->coverurl = $coverurl;
    }

    public function setQuotation(array $quotation): void
    {
        $this->quotation = $quotation;
    }

    public function setPopularity(int $popularity): void
    {
        $this->popularity = $popularity;
    }

    public function toArray() {
        return [
            'countrycode' => $this->countrycode,
            'publicationcode' => $this->publicationcode,
            'publicationtitle' => $this->publicationtitle,
            'issuenumber' => $this->issuenumber,
            'coverid' => $this->coverid,
            'coverurl' => $this->coverurl,
            'quotation' => $this->quotation,
            'popularity' => $this->popularity
        ];
    }
}
