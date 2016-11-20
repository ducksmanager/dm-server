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


}

