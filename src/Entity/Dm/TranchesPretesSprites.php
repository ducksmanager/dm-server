<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesSprites
 *
 * @ORM\Table(name="tranches_pretes_sprites", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_pretes_sprites_unique", columns={"ID_Tranche", "Sprite_name"})}, indexes={@ORM\Index(name="IDX_C5A7B720BAEB49DF", columns={"ID_Tranche"})})
 * @ORM\Entity
 */
class TranchesPretesSprites
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
     * @var \TranchesPretes
     *
     * @ORM\ManyToOne(targetEntity="TranchesPretes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Tranche", referencedColumnName="ID")
     * })
     */
    private $idTranche;

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

    public function getIdTranche(): ?TranchesPretes
    {
        return $this->idTranche;
    }

    public function setIdTranche(?TranchesPretes $idTranche): self
    {
        $this->idTranche = $idTranche;

        return $this;
    }


}
