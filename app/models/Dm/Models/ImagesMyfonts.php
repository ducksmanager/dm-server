<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesMyfonts
 *
 * @ORM\Table(name="images_myfonts", uniqueConstraints={@ORM\UniqueConstraint(name="ID", columns={"ID"})})
 * @ORM\Entity
 * @deprecated
 */
class ImagesMyfonts extends \Dm\Models\BaseModel
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
     * @var string|null
     *
     * @ORM\Column(name="Font", type="string", length=150, nullable=true)
     */
    private $font;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Color", type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ColorBG", type="string", length=10, nullable=true)
     */
    private $colorbg;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Width", type="string", length=7, nullable=true)
     */
    private $width;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Texte", type="string", length=150, nullable=true)
     */
    private $texte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Precision_", type="string", length=5, nullable=true)
     */
    private $precision;



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
     * Set font.
     *
     * @param string|null $font
     *
     * @return ImagesMyfonts
     */
    public function setFont($font = null)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get font.
     *
     * @return string|null
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return ImagesMyfonts
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set colorbg.
     *
     * @param string|null $colorbg
     *
     * @return ImagesMyfonts
     */
    public function setColorbg($colorbg = null)
    {
        $this->colorbg = $colorbg;

        return $this;
    }

    /**
     * Get colorbg.
     *
     * @return string|null
     */
    public function getColorbg()
    {
        return $this->colorbg;
    }

    /**
     * Set width.
     *
     * @param string|null $width
     *
     * @return ImagesMyfonts
     */
    public function setWidth($width = null)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width.
     *
     * @return string|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set texte.
     *
     * @param string|null $texte
     *
     * @return ImagesMyfonts
     */
    public function setTexte($texte = null)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte.
     *
     * @return string|null
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set precision.
     *
     * @param string|null $precision
     *
     * @return ImagesMyfonts
     */
    public function setPrecision($precision = null)
    {
        $this->precision = $precision;

        return $this;
    }

    /**
     * Get precision.
     *
     * @return string|null
     */
    public function getPrecision()
    {
        return $this->precision;
    }
}
