<?php

namespace App\EntityTransform;


class NumeroSimple
{
    private $numero;
    private $etat;

    /**
     * @param $numero
     * @param $etat
     */
    public function __construct($numero, $etat)
    {
        $this->numero = $numero;
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}