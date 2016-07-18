<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretes
 *
 * @ORM\Table(name="tranches_pretes")
 * @ORM\Entity
 */
class TranchesPretes extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber = '';

    /**
     * @var string
     *
     * @ORM\Column(name="photographes", type="text", length=65535, nullable=true)
     */
    private $photographes;

    /**
     * @var string
     *
     * @ORM\Column(name="createurs", type="text", length=65535, nullable=true)
     */
    private $createurs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateajout", type="datetime", nullable=false)
     */
    private $dateajout = 'CURRENT_TIMESTAMP';


}

