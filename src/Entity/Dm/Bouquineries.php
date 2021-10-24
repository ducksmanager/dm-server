<?php

namespace App\Entity\Dm;

use App\Entity\EdgeCreator\TranchesEnCoursModelesImages;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\PersistentCollection;

/**
 * Bouquineries
 *
 * @ORM\Table(name="bouquineries")
 * @ORM\Entity
 */
class Bouquineries
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
     * @ORM\Column(name="Nom", type="string", length=25, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseComplete", type="text", length=65535, nullable=false)
     */
    private $address;

    /**
     * @var float
     *
     * @ORM\Column(name="CoordX", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordX = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="CoordY", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordY = 0;

    /**
     * @OneToMany(fetch="EAGER", targetEntity="BouquineriesCommentaires", cascade={"persist", "remove"}, mappedBy="bookstore")
     */
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCoordX(): ?float
    {
        return $this->coordX;
    }

    public function setCoordX(float $coordX): self
    {
        $this->coordX = $coordX;

        return $this;
    }

    public function getCoordY(): ?float
    {
        return $this->coordY;
    }

    public function setCoordY(float $coordY): self
    {
        $this->coordY = $coordY;

        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(BouquineriesCommentaires $comment): self
    {
        $this->comments->add($comment);

        return $this;
    }
}
