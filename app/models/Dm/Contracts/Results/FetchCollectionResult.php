<?php
namespace Dm\Contracts\Results;

use Doctrine\Common\Collections\ArrayCollection;
use Dm\Contracts\Dtos\PublicationCollection;
use Generic\Contracts\Results\GenericReturnObjectInterface;

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
    public function getNumeros()
    {
        return $this->numeros;
    }

    /**
     * @param ArrayCollection $numeros
     */
    public function setNumeros($numeros)
    {
        $this->numeros = $numeros;
    }

    /**
     * @return CoaDataResult
     */
    public function getStatic()
    {
        return $this->static;
    }

    /**
     * @param CoaDataResult $static
     */
    public function setStatic(CoaDataResult $static)
    {
        $this->static = $static;
    }

    public function toArray() {
        return [
            'static' => $this->static->toArray(),
            'numeros' => $this->numeros->toArray()
        ];
    }
}