<?php

namespace DmStats\Contracts\Results;


use Generic\Contracts\Results\GenericReturnObjectInterface;

class SuggestedIssue implements GenericReturnObjectInterface {
    var $storycode;
    var $storycomment;
    var $title;
    var $personcode;
    var $personfullname;

    public static function build($storycode, $storycomment, $title, $personcode, $personfullname)
    {
        $o = new SuggestedIssue();
        $o->storycode = $storycode;
        $o->storycomment = $storycomment;
        $o->title = $title;
        $o->personcode = $personcode;
        $o->personfullname = $personfullname;

        return $o;
    }


    public function toArray() {
        return [
            'story' => [
                'storycode' => $this->storycode,
                'storycomment' => $this->storycomment,
                'title' => $this->title
            ],
            'author' => [
                'personcode' => $this->personcode,
                'fullname' => $this->personfullname
            ]
        ];
    }
}