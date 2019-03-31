<?php
namespace App\EntityTransform;

use App\Controller\GenericReturnObjectInterface;
use Doctrine\Common\Collections\ArrayCollection;

class CoaDataResult implements GenericReturnObjectInterface
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
    public function getPays(): ArrayCollection
    {
        return $this->pays;
    }

    /**
     * @param ArrayCollection $pays
     */
    public function setPays($pays): void
    {
        $this->pays = $pays;
    }

    /**
     * @return ArrayCollection
     */
    public function getMagazines(): ArrayCollection
    {
        return $this->magazines;
    }

    /**
     * @param ArrayCollection $magazines
     */
    public function setMagazines($magazines): void
    {
        $this->magazines = $magazines;
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

    public function toArray() : array {
        return [
            'pays' => $this->pays->toArray(),
            'magazines' => $this->magazines->toArray(),
            'numeros' => $this->numeros->toArray(),
        ];
    }
}