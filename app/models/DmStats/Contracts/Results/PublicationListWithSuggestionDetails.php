<?php

namespace DmStats\Contracts\Results;


class PublicationListWithSuggestionDetails
{
    /** @var Story[] $stories */
    private $stories = [];

    static $authors;
    static $storyDetails;

    public function __construct()
    {
    }

    public function addStory($publicationcode, $issuenumber, $personcode, $storycode) {
        $this->stories
            [implode(' ', [$publicationcode, $issuenumber])]
                [$storycode] = Story::build(
                    $storycode,
                    self::$storyDetails[$storycode]['storycomment'],
                    self::$storyDetails[$storycode]['title'],
                    $personcode,
                    self::$authors[$personcode]
                )->toArray();
    }

    public function getStories()
    {
        return $this->stories;
    }
}