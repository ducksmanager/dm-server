<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesSpritesSize
 *
 * @ORM\Table(name="tranches_pretes_sprites_size")
 * @ORM\Entity
 */
class TranchesPretesSpritesSize
{
    /**
     * @var string
     *
     * @ORM\Column(name="sprite_name", type="string", length=25, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $spriteName;

    /**
     * @var int|null
     *
     * @ORM\Column(name="size", type="integer", nullable=true)
     */
    private $size;

    public function getSpriteName(): ?string
    {
        return $this->spriteName;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }


}
