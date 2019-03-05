<?php

namespace App\Entity\Coverid;

use Doctrine\ORM\Mapping as ORM;

/**
 * Covers
 *
 * @ORM\Table(name="covers", uniqueConstraints={@ORM\UniqueConstraint(name="uniquefieldset_covers", columns={"issuecode", "url"})})
 * @ORM\Entity
 */
class Covers
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
     * @ORM\Column(name="issuecode", type="string", length=17, nullable=false)
     */
    private $issuecode;

    /**
     * @var string
     *
     * @ORM\Column(name="sitecode", type="string", length=11, nullable=false)
     */
    private $sitecode;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=98, nullable=false)
     */
    private $url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssuecode(): ?string
    {
        return $this->issuecode;
    }

    public function setIssuecode(string $issuecode): self
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    public function getSitecode(): ?string
    {
        return $this->sitecode;
    }

    public function setSitecode(string $sitecode): self
    {
        $this->sitecode = $sitecode;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }


}
