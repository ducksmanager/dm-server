<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesContributeurs
 *
 * @ORM\Table(name="tranches_pretes_contributeurs", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQUE", columns={"publicationcode", "issuenumber", "contributeur", "contribution"})}, indexes={@ORM\Index(name="tranches_pretes_contributeurs_publicationcode_issuenumber_index", columns={"publicationcode", "issuenumber"}), @ORM\Index(name="tranches_pretes_contributeurs_contributeur_index", columns={"contributeur"})})
 * @ORM\Entity
 * @deprecated
 */
class TranchesPretesContributeurs
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
     * @ORM\Column(name="publicationcode", type="string", length=15, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=30, nullable=false)
     */
    private $issuenumber;

    /**
     * @var int
     *
     * @ORM\Column(name="contributeur", type="integer", nullable=false)
     */
    private $contributeur;

    /**
     * @var string
     *
     * @ORM\Column(name="contribution", type="string", length=0, nullable=false, options={"default"="createur"})
     */
    private $contribution = 'createur';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function setPublicationcode(string $publicationcode): self
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function setIssuenumber(string $issuenumber): self
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    public function getContributeur(): ?int
    {
        return $this->contributeur;
    }

    public function setContributeur(int $contributeur): self
    {
        $this->contributeur = $contributeur;

        return $this;
    }

    public function getContribution(): ?string
    {
        return $this->contribution;
    }

    public function setContribution(string $contribution): self
    {
        $this->contribution = $contribution;

        return $this;
    }


}
