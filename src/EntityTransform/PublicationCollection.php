<?php

namespace App\EntityTransform;


use Doctrine\Common\Collections\ArrayCollection;

class PublicationCollection extends ArrayCollection
{
    public function toArray() : array
    {
        $arrayElements = [];

        /** @var ArrayCollection[] $elements */
        $elements = parent::toArray();
        foreach($elements as $publicationCode => $issues) {
            $arrayElements[$publicationCode] = [];
            $issuesArray = $issues->toArray();

            /** @var NumeroSimple[] $issuesArray */
            foreach($issuesArray as $issue) {
                $arrayElements[$publicationCode] []= $issue->toArray();
            }
        }
        return $arrayElements;
    }

}