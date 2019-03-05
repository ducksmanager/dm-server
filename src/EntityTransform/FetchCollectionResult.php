<?php
namespace App\EntityTransform;

use App\Controller\GenericReturnObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;

class FetchCollectionResult implements GenericReturnObjectInterface
{
    /** @var PublicationCollection $numeros */
    private $numeros;

    /** @var CoaDataResult $static */
    private $static;

    public function __construct()
    {
        $this->numeros = new PublicationCollection();
        $this->static = new CoaDataResult();
    }

    /**
     * @return ArrayCollection
     */
    public function getNumeros(): ArrayCollection
    {
        return $this->numeros;
    }

    /**
     * @param ArrayCollection $numeros
     */
    public function setNumeros($numeros): void
    {
        $this->numeros = $numeros;
    }

    /**
     * @return CoaDataResult
     */
    public function getStatic(): CoaDataResult
    {
        return $this->static;
    }

    /**
     * @param CoaDataResult $static
     */
    public function setStatic(CoaDataResult $static): void
    {
        $this->static = $static;
    }

    public function toArray() : array {
        return [
            'static' => $this->static->toArray(),
            'numeros' => $this->numeros->toArray()
        ];
    }
}