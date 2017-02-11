<?php

namespace DmStats\Contracts\Results;


class SuggestedStory {
    var $storycode;
    var $storycomment;
    var $title;
    var $personcode;
    var $personfullname;
    var $score;

    public static function build($storycode, $storycomment, $title, $personcode, $personfullname, $score)
    {
        $o = new SuggestedStory();
        $o->storycode = $storycode;
        $o->storycomment = $storycomment;
        $o->title = $title;
        $o->personcode = $personcode;
        $o->personfullname = $personfullname;
        $o->score = $score;

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
            ],
            'score' => $this->score
        ];
    }
}