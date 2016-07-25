<?php

namespace Wtd\models\Wtd\Contracts\Dtos;


use Doctrine\Common\Collections\ArrayCollection;

class PublicationCollection extends ArrayCollection
{
    public function toArray()
    {
        $arrayElements = [];

        $elements = parent::toArray();
        foreach($elements as $publicationCode => $issues) {
            $arrayElements[$publicationCode] = [];
            $issuesArray = $issues->toArray();
            foreach($issuesArray as $issue) {
                $arrayElements[$publicationCode] []= $issue->toArray();
            }
        }
        return $arrayElements;
    }

}