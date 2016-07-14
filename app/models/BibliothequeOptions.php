<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeOptions
 *
 * @ORM\Table(name="bibliotheque_options")
 * @ORM\Entity
 */
class BibliothequeOptions
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
     * @var boolean
     *
     * @ORM\Column(name="CouleurR", type="boolean", nullable=true)
     */
    private $couleurr = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="CouleurG", type="boolean", nullable=true)
     */
    private $couleurg = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="CouleurB", type="boolean", nullable=true)
     */
    private $couleurb = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="Autre", type="text", length=65535, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $autre;


}

