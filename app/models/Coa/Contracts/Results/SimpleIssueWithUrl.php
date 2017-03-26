<?php

namespace Coa\Contracts\Results;


use Generic\Contracts\Results\GenericReturnObject;

class SimpleIssueWithUrl implements GenericReturnObject
{
    private $countrycode;
    private $publicationtitle;
    private $issuenumber;
    private $fullurl;

    public static function buildWithoutUrl($countrycode, $publicationtitle, $issuenumber)
    {
        $o = new SimpleIssueWithUrl();
        $o->countrycode = $countrycode;
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
     * @return mixed
     */
    public function getFullurl()
    {
        return $this->fullurl;
    }

    /**
     * @param mixed $fullurl
     */
    public function setFullurl($fullurl)
    {
        $this->fullurl = $fullurl;
    }


    public function toArray() {
        return [
            'countrycode' => $this->getCountrycode(),
            'publicationtitle' => $this->getPublicationtitle(),
            'issuenumber' => $this->getIssuenumber(),
            'fullurl' => $this->getFullurl()
        ];
    }
}