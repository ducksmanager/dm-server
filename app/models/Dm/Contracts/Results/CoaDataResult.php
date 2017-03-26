<?php
namespace Dm\Contracts\Results;


use Doctrine\Common\Collections\ArrayCollection;
use Generic\Contracts\Results\GenericReturnObject;

class CoaDataResult implements GenericReturnObject
{
    /** @var ArrayCollection $pays */
    protected $pays;

    /** @var ArrayCollection $magazines */
    protected $magazines;

    /** @var ArrayCollection $numeros */
    protected $numeros;

    /**
     * CoaDataResult constructor.
     */
    public function __construct()
    {
        $this->pays = new ArrayCollection();
        $this->magazines = new ArrayCollection();
        $this->numeros = new ArrayCollection();
    }


    /**
     * @return ArrayCollection
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * @param ArrayCollection $pays
     */
    public function setPays($pays)
    {
        $this->pays = $pays;
    }

    /**
     * @return ArrayCollection
     */
    public function getMagazines()
    {
        return $this->magazines;
    }

    /**
     * @param ArrayCollection $magazines
     */
    public function setMagazines($magazines)
    {
        $this->magazines = $magazines;
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

    public function toArray() {
        return [
            'pays' => $this->pays->toArray(),
            'magazines' => $this->magazines->toArray(),
            'numeros' => $this->numeros->toArray(),
        ];
    }
}