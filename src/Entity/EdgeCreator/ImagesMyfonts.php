<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesMyfonts
 *
 * @ORM\Table(name="images_myfonts")
 * @ORM\Entity
 */
class ImagesMyfonts
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFont(): ?string
    {
        return $this->font;
    }

    public function setFont(?string $font): self
    {
        $this->font = $font;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getColorbg(): ?string
    {
        return $this->colorbg;
    }

    public function setColorbg(?string $colorbg): self
    {
        $this->colorbg = $colorbg;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(?string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(?string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getPrecision(): ?string
    {
        return $this->precision;
    }

    public function setPrecision(?string $precision): self
    {
        $this->precision = $precision;

        return $this;
    }


}
