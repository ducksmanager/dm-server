<?php

namespace App\Entity\DmStats;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoiresPublications
 *
 * @ORM\Table(name="histoires_publications", uniqueConstraints={@ORM\UniqueConstraint(name="publicationcode", columns={"publicationcode", "issuenumber", "storycode"})}, indexes={@ORM\Index(name="index_story", columns={"storycode"}), @ORM\Index(name="index_issue", columns={"publicationcode", "issuenumber"})})
 * @ORM\Entity
 */
class HistoiresPublications
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
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     */
    private $storycode;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     */
    private $issuenumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStorycode(): ?string
    {
        return $this->storycode;
    }

    public function setStorycode(string $storycode): self
    {
        $this->storycode = $storycode;

        return $this;
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


}
