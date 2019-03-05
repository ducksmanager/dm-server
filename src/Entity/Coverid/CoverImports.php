<?php

namespace App\Entity\Coverid;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoverImports
 *
 * @ORM\Table(name="cover_imports", uniqueConstraints={@ORM\UniqueConstraint(name="uniquefieldset_cover_imports", columns={"coverid", "imported", "import_error"})})
 * @ORM\Entity
 */
class CoverImports
{
    /**
     * @var int
     *
     * @ORM\Column(name="coverid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coverid;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="imported", type="datetime", nullable=true)
     */
    private $imported;

    /**
     * @var string|null
     *
     * @ORM\Column(name="import_error", type="string", length=200, nullable=true)
     */
    private $importError;

    public function getCoverid(): ?int
    {
        return $this->coverid;
    }

    public function getImported(): ?\DateTimeInterface
    {
        return $this->imported;
    }

    public function setImported(?\DateTimeInterface $imported): self
    {
        $this->imported = $imported;

        return $this;
    }

    public function getImportError(): ?string
    {
        return $this->importError;
    }

    public function setImportError(?string $importError): self
    {
        $this->importError = $importError;

        return $this;
    }


}
