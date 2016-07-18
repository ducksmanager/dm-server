<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magazines
 *
 * @ORM\Table(name="magazines")
 * @ORM\Entity
 */
class Magazines extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="PaysAbrege", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $paysabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAbrege", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomComplet", type="string", length=70, nullable=false)
     */
    private $nomcomplet;

    /**
     * @var string
     *
     * @ORM\Column(name="RedirigeDepuis", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $redirigedepuis;

    /**
     * @var boolean
     *
     * @ORM\Column(name="NeParaitPlus", type="boolean", nullable=true)
     */
    private $neparaitplus;


}

