<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeContributeurs
 *
 * @ORM\Table(name="bibliotheque_contributeurs")
 * @ORM\Entity
 */
class BibliothequeContributeurs
{
    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="text", length=65535, nullable=true)
     */
    private $texte;


}

