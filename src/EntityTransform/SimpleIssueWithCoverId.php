<?php

namespace App\EntityTransform;

use App\Helper\GenericReturnObjectInterface;

class SimpleIssueWithCoverId implements GenericReturnObjectInterface
{
    private $countrycode;
    private $publicationcode;
    private $publicationtitle;
    private $issuenumber;
    private $coverid;
    private $coverurl;

    public static function buildWithoutCoverId($countrycode, $publicationcode, $publicationtitle, $issuenumber)
    {
        $o = new self();
        $o->countrycode = $countrycode;
        $o->publicationcode = $publicationcode;
        $o->publicationtitle = $publicationtitle;
        $o->issuenumber = $issuenumber;

        return $o;
    }

    /**
     * SimpleIssue constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getCountrycode()
    {
        return $this->countrycode;
    }

    /**
     * @param mixed $countrycode
     */
    public function setCountrycode($countrycode)
    {
        $this->countrycode = $countrycode;
    }

    /**
     * @return mixed
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * @param mixed $publicationcode
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;
    }

    /**
     * @return mixed
     */
    public function getPublicationtitle()
    {
        return $this->publicationtitle;
    }

    /**
     * @param mixed $publicationtitle
     */
    public function setPublicationtitle($publicationtitle)
    {
        $this->publicationtitle = $publicationtitle;
    }

    /**
     * @return mixed
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * @param mixed $issuenumber
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;
    }

    /**
     * @return integer
     */
    public function getCoverid()
    {
        return $this->coverid;
    }

    /**
     * @param integer $coverid
     */
    public function setCoverid(int $coverid)
    {
        $this->coverid = $coverid;
    }

    public function getCoverurl() : string
    {
        return $this->coverurl;
    }

    /**
     * @param string $coverurl
     */
    public function setCoverurl(string $coverurl): void
    {
        $this->coverurl = $coverurl;
    }



    public function toArray() {
        return [
            'countrycode' => $this->getCountrycode(),
            'publicationcode' => $this->getPublicationcode(),
            'publicationtitle' => $this->getPublicationtitle(),
            'issuenumber' => $this->getIssuenumber(),
            'coverid' => $this->getCoverid(),
            'coverurl' => $this->getCoverurl()
        ];
    }
}
