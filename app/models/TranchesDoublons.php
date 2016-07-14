<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesDoublons
 *
 * @ORM\Table(name="tranches_doublons")
 * @ORM\Entity
 */
class TranchesDoublons
{
    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $magazine;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="NumeroReference", type="string", length=8, nullable=false)
     */
    private $numeroreference;


}

