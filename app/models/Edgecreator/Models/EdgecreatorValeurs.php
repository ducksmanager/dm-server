<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorValeurs
 *
 * @ORM\Table(name="edgecreator_valeurs")
 * @ORM\Entity
 */
class EdgecreatorValeurs extends \Edgecreator\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ID_Option", type="integer", nullable=true)
     */
    private $idOption;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Option_valeur", type="string", length=200, nullable=true)
     */
    private $optionValeur;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idOption.
     *
     * @param int|null $idOption
     *
     * @return EdgecreatorValeurs
     */
    public function setIdOption($idOption = null)
    {
        $this->idOption = $idOption;

        return $this;
    }

    /**
     * Get idOption.
     *
     * @return int|null
     */
    public function getIdOption()
    {
        return $this->idOption;
    }

    /**
     * Set optionValeur.
     *
     * @param string|null $optionValeur
     *
     * @return EdgecreatorValeurs
     */
    public function setOptionValeur($optionValeur = null)
    {
        $this->optionValeur = $optionValeur;

        return $this;
    }

    /**
     * Get optionValeur.
     *
     * @return string|null
     */
    public function getOptionValeur()
    {
        return $this->optionValeur;
    }
}
