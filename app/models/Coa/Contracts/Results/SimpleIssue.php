<?php

namespace Wtd\models\Coa\Contracts\Results;


class SimpleIssue
{
    private $countrycode;
    private $publicationtitle;
    private $issuenumber;

    /**
     * SimpleIssue constructor.
     * @param $countrycode
     * @param $publicationtitle
     * @param $issuenumber
     */
    public function __construct($countrycode, $publicationtitle, $issuenumber)
    {
        $this->countrycode = $countrycode;
        $this->publicationtitle = $publicationtitle;
        $this->issuenumber = $issuenumber;
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

    public function toArray() {
        return [
            'countrycode' => $this->getCountrycode(),
            'publicationtitle' => $this->getPublicationtitle(),
            'issuenumber' => $this->getIssuenumber()
        ];
    }
}