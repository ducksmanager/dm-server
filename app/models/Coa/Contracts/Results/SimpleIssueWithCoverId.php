<?php

namespace Coa\Contracts\Results;


use Generic\Contracts\Results\GenericReturnObject;

class SimpleIssueWithCoverId implements GenericReturnObject
{
    private $countrycode;
    private $publicationtitle;
    private $issuenumber;
    private $coverid;

    public static function buildWithoutCoverId($countrycode, $publicationtitle, $issuenumber)
    {
        $o = new SimpleIssueWithCoverId();
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
     * @return integer
     */
    public function getCoverid()
    {
        return $this->coverid;
    }

    /**
     * @param integer $coverid
     */
    public function setCoverid($coverid)
    {
        $this->coverid = $coverid;
    }


    public function toArray() {
        return [
            'countrycode' => $this->getCountrycode(),
            'publicationtitle' => $this->getPublicationtitle(),
            'issuenumber' => $this->getIssuenumber(),
            'coverid' => $this->getCoverid()
        ];
    }
}