<?php

namespace Wtd\models\Wtd\Contracts\Dtos;


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
    public function setNumero($numero)
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
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }
}