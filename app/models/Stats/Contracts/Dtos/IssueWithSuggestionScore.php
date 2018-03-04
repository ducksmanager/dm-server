<?php

namespace Stats\Contracts\Dtos;


class IssueWithSuggestionScore
{
    private $publicationcode;
    private $issuenumber;
    private $score;

    public static function build($publicationcode, $issuenumber, $score)
    {
        $o = new self();
        $o->publicationcode = $publicationcode;
        $o->issuenumber = $issuenumber;
        $o->score = $score;

        return $o;
    }

    /**
     * SimpleIssue constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * @param string $publicationcode
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;
    }

    /**
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * @param string $issuenumber
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;
    }

    /**
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param integer $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }


    public function toArray() {
        return [
            'publicationcode' => $this->getPublicationcode(),
            'issuenumber' => $this->getIssuenumber(),
            'score' => $this->getScore()
        ];
    }
}
