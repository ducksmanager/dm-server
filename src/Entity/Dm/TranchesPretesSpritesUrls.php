<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesSpritesUrls
 *
 * @ORM\Table(name="tranches_pretes_sprites_urls", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_pretes_sprites_urls_unique", columns={"Sprite_name", "Version"})})
 * @ORM\Entity
 */
class TranchesPretesSpritesUrls
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
     * @var string
     *
     * @ORM\Column(name="Sprite_name", type="string", length=25, nullable=false)
     */
    private $spriteName;

    /**
     * @var string
     *
     * @ORM\Column(name="Version", type="string", length=12, nullable=false)
     */
    private $version;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpriteName(): ?string
    {
        return $this->spriteName;
    }

    public function setSpriteName(string $spriteName): self
    {
        $this->spriteName = $spriteName;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }


}
