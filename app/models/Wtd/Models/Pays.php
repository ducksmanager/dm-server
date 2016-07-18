<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pays
 *
 * @ORM\Table(name="pays")
 * @ORM\Entity
 */
class Pays extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="NomAbrege", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomComplet", type="string", length=60, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomcomplet;

    /**
     * @var string
     *
     * @ORM\Column(name="L10n", type="string", length=5, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $l10n = 'fr';


}

