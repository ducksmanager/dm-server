<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesContributeurs
 *
 * @ORM\Table(name="tranches_pretes_contributeurs", indexes={@ORM\Index(name="tranches_pretes_contributeurs_contributeur_index", columns={"contributeur"}), @ORM\Index(name="tranches_pretes_contributeurs_publicationcode_issuenumber_index", columns={"publicationcode", "issuenumber"})})
 * @ORM\Entity
 */
class TranchesPretesContributeurs
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=15, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber;

    /**
     * @var int
     *
     * @ORM\Column(name="contributeur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contributeur;

    /**
     * @var string
     *
     * @ORM\Column(name="contribution", type="string", length=0, nullable=false, options={"default"="createur"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contribution = 'createur';

    public function getPublicationcode(): ?string
    {
        return $this->publicationcode;
    }

    public function getIssuenumber(): ?string
    {
        return $this->issuenumber;
    }

    public function getContributeur(): ?int
    {
        return $this->contributeur;
    }

    public function getContribution(): ?string
    {
        return $this->contribution;
    }


}
