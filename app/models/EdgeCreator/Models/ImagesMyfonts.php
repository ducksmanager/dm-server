<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesMyfonts
 *
 * @ORM\Table(name="images_myfonts", uniqueConstraints={@ORM\UniqueConstraint(name="images_myfonts_ID_uindex", columns={"ID"})})
 * @ORM\Entity
 */
class ImagesMyfonts extends \EdgeCreator\Models\BaseModel
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
     * @var string
     *
     * @ORM\Column(name="Font", type="string", length=150, nullable=true)
     */
    private $font;

    /**
     * @var string
     *
     * @ORM\Column(name="Color", type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="ColorBG", type="string", length=10, nullable=true)
     */
    private $colorbg;

    /**
     * @var string
     *
     * @ORM\Column(name="Width", type="string", length=7, nullable=true)
     */
    private $width;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="string", length=150, nullable=true)
     */
    private $texte;

    /**
     * @var string
     *
     * @ORM\Column(name="Precision_", type="string", length=5, nullable=true)
     */
    private $precision;



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
     * Set font
     *
     * @param string $font
     *
     * @return ImagesMyfonts
     */
    public function setFont($font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get font
     *
     * @return string
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return ImagesMyfonts
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set colorbg
     *
     * @param string $colorbg
     *
     * @return ImagesMyfonts
     */
    public function setColorbg($colorbg)
    {
        $this->colorbg = $colorbg;

        return $this;
    }

    /**
     * Get colorbg
     *
     * @return string
     */
    public function getColorbg()
    {
        return $this->colorbg;
    }

    /**
     * Set width
     *
     * @param string $width
     *
     * @return ImagesMyfonts
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set texte
     *
     * @param string $texte
     *
     * @return ImagesMyfonts
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set precision
     *
     * @param string $precision
     *
     * @return ImagesMyfonts
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Get precision
     *
     * @return string
     */
    public function getPrecision()
    {
        return $this->precision;
    }
}
