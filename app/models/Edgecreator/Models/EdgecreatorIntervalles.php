<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorIntervalles
 *
 * @ORM\Table(name="edgecreator_intervalles", indexes={@ORM\Index(name="index_intervalles", columns={"ID_Valeur", "Numero_debut", "Numero_fin", "username"})})
 * @ORM\Entity
 */
class EdgecreatorIntervalles extends \Edgecreator\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Valeur", type="integer", nullable=false)
     */
    private $idValeur;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero_debut", type="string", length=10, nullable=false)
     */
    private $numeroDebut;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero_fin", type="string", length=10, nullable=false)
     */
    private $numeroFin;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=false)
     */
    private $username;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idValeur
     *
     * @param integer $idValeur
     *
     * @return EdgecreatorIntervalles
     */
    public function setIdValeur($idValeur)
    {
        $this->idValeur = $idValeur;

        return $this;
    }

    /**
     * Get idValeur
     *
     * @return integer
     */
    public function getIdValeur()
    {
        return $this->idValeur;
    }

    /**
     * Set numeroDebut
     *
     * @param string $numeroDebut
     *
     * @return EdgecreatorIntervalles
     */
    public function setNumeroDebut($numeroDebut)
    {
        $this->numeroDebut = $numeroDebut;

        return $this;
    }

    /**
     * Get numeroDebut
     *
     * @return string
     */
    public function getNumeroDebut()
    {
        return $this->numeroDebut;
    }

    /**
     * Set numeroFin
     *
     * @param string $numeroFin
     *
     * @return EdgecreatorIntervalles
     */
    public function setNumeroFin($numeroFin)
    {
        $this->numeroFin = $numeroFin;

        return $this;
    }

    /**
     * Get numeroFin
     *
     * @return string
     */
    public function getNumeroFin()
    {
        return $this->numeroFin;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return EdgecreatorIntervalles
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
}
