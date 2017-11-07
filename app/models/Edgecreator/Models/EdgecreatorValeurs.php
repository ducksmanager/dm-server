<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * EdgecreatorValeurs
 *
 * @ORM\Table(name="edgecreator_valeurs")
 * @ORM\Entity
 */
class EdgecreatorValeurs extends \EdgeCreator\Models\BaseModel
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
     * @ORM\Column(name="ID_Option", type="integer", nullable=true)
     */
    private $idOption;

    /**
     * @var string
     *
     * @ORM\Column(name="Option_valeur", type="string", length=200, nullable=true)
     */
    private $optionValeur;



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
     * Set idOption
     *
     * @param integer $idOption
     *
     * @return EdgecreatorValeurs
     */
    public function setIdOption($idOption)
    {
        $this->idOption = $idOption;

        return $this;
    }

    /**
     * Get idOption
     *
     * @return integer
     */
    public function getIdOption()
    {
        return $this->idOption;
    }

    /**
     * Set optionValeur
     *
     * @param string $optionValeur
     *
     * @return EdgecreatorValeurs
     */
    public function setOptionValeur($optionValeur)
    {
        $this->optionValeur = $optionValeur;

        return $this;
    }

    /**
     * Get optionValeur
     *
     * @return string
     */
    public function getOptionValeur()
    {
        return $this->optionValeur;
    }
}
