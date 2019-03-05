<?php

namespace App\EntityTransform;


class SimilarImagesResult
{
    private $boundingRectHeight;
    private $boundingRectWidth;
    private $boundingRectX;
    private $boundingRectY;
    private $imageIds = [];
    private $scores = [];
    private $tags = [];
    private $type;

    /**
     * @param string $jsonEncodedResult
     * @return SimilarImagesResult
     */
    public static function createFromJsonEncodedResult($jsonEncodedResult): SimilarImagesResult
    {
        $resultArray = json_decode($jsonEncodedResult, true);
        if (is_null($resultArray)) {
            return null;
        }

        $outputObject = new self();
        $outputObject->setType($resultArray['type']);

        if (array_key_exists('bounding_rects', $resultArray)) {
            $boundingRect = $resultArray['bounding_rects'];

            $outputObject->setBoundingRectHeight($boundingRect['height']);
            $outputObject->setBoundingRectWidth($boundingRect['width']);
            $outputObject->setBoundingRectX($boundingRect['x']);
            $outputObject->setBoundingRectY($boundingRect['y']);

            $outputObject->setImageIds(array_slice($resultArray['image_ids'], 0, 10, true));
            $outputObject->setScores(array_slice($resultArray['scores'], 0, 10, true));
            $outputObject->setTags($resultArray['tags']);
        }

        return $outputObject;
    }

    /**
     * @return mixed
     */
    public function getBoundingRectHeight()
    {
        return $this->boundingRectHeight;
    }

    /**
     * @param mixed $boundingRectHeight
     */
    public function setBoundingRectHeight($boundingRectHeight): void
    {
        $this->boundingRectHeight = $boundingRectHeight;
    }

    /**
     * @return mixed
     */
    public function getBoundingRectWidth()
    {
        return $this->boundingRectWidth;
    }

    /**
     * @param mixed $boundingRectWidth
     */
    public function setBoundingRectWidth($boundingRectWidth): void
    {
        $this->boundingRectWidth = $boundingRectWidth;
    }

    /**
     * @return mixed
     */
    public function getBoundingRectX()
    {
        return $this->boundingRectX;
    }

    /**
     * @param mixed $boundingRectX
     */
    public function setBoundingRectX($boundingRectX): void
    {
        $this->boundingRectX = $boundingRectX;
    }

    /**
     * @return mixed
     */
    public function getBoundingRectY()
    {
        return $this->boundingRectY;
    }

    /**
     * @param mixed $boundingRectY
     */
    public function setBoundingRectY($boundingRectY): void
    {
        $this->boundingRectY = $boundingRectY;
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