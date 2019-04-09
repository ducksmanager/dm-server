<?php

namespace App\EntityTransform;

class SimilarImagesResult
{
    private $imageIds = [];
    private $scores = [];
    private $tags = [];
    private $type;

    public static function createFromJsonEncodedResult(string $jsonEncodedResult): ?SimilarImagesResult
    {
        $resultArray = json_decode($jsonEncodedResult, true);
        if (is_null($resultArray)) {
            return null;
        }

        $outputObject = new self();
        $outputObject->setType($resultArray['type']);

        if (array_key_exists('bounding_rects', $resultArray)) {
            $boundingRect = $resultArray['bounding_rects'];
            if (count($resultArray['image_ids']) === 0) {
                return null;
            }

            $outputObject->setImageIds(array_slice($resultArray['image_ids'], 0, 10, true));
            $outputObject->setScores(array_slice($resultArray['scores'], 0, 10, true));
            $outputObject->setTags($resultArray['tags']);
        }

        return $outputObject;
    }

    /**
     * @return array
     */
    public function getImageIds(): array
    {
        return $this->imageIds;
    }

    /**
     * @param array $imageIds
     */
    public function setImageIds(array $imageIds): void
    {
        $this->imageIds = $imageIds;
    }

    /**
     * @return array
     */
    public function getScores(): array
    {
        return $this->scores;
    }

    /**
     * @param array $scores
     */
    public function setScores(array $scores): void
    {
        $this->scores = $scores;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

}