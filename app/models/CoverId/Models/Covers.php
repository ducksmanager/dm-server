<?php

namespace CoverId\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Covers
 *
 * @ORM\Table(name="covers", uniqueConstraints={@ORM\UniqueConstraint(name="uniquefieldset", columns={"issuecode", "url"})})
 * @ORM\Entity
 */
class Covers extends \CoverId\Models\BaseModel
{
    /**
     * @var integer
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
     * @ORM\Column(name="url", type="string", length=98, nullable=false)
     */
    private $url;


}

