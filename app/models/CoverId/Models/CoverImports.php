<?php

namespace CoverId\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * CoverImports
 *
 * @ORM\Table(name="cover_imports", uniqueConstraints={@ORM\UniqueConstraint(name="uniquefieldset", columns={"coverid", "imported", "import_error"})})
 * @ORM\Entity
 */
class CoverImports extends \CoverId\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="coverid", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $coverid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="imported", type="datetime", nullable=true)
     */
    private $imported;

    /**
     * @var string
     *
     * @ORM\Column(name="import_error", type="string", length=200, nullable=true)
     */
    private $importError;



    /**
     * Get coverid
     *
     * @return integer
     */
    public function getCoverid()
    {
        return $this->coverid;
    }

    /**
     * Set imported
     *
     * @param \DateTime $imported
     *
     * @return CoverImports
     */
    public function setImported($imported)
    {
        $this->imported = $imported;

        return $this;
    }

    /**
     * Get imported
     *
     * @return \DateTime
     */
    public function getImported()
    {
        return $this->imported;
    }

    /**
     * Set importError
     *
     * @param string $importError
     *
     * @return CoverImports
     */
    public function setImportError($importError)
    {
        $this->importError = $importError;

        return $this;
    }

    /**
     * Get importError
     *
     * @return string
     */
    public function getImportError()
    {
        return $this->importError;
    }
}
