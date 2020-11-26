<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demo
 *
 * @ORM\Table(name="demo")
 * @ORM\Entity
 */
class Demo
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer", nullable=false)
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateDernierInit", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $datedernierinit = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDatedernierinit(): ?\DateTimeInterface
    {
        return $this->datedernierinit;
    }

    public function setDatedernierinit(\DateTimeInterface $datedernierinit): self
    {
        $this->datedernierinit = $datedernierinit;

        return $this;
    }

}
