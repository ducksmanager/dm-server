<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosRecommandes
 *
 * @ORM\Table(name="numeros_recommandes")
 * @ORM\Entity
 */
class NumerosRecommandes
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
     * @ORM\Column(name="Numero", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $numero;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Notation", type="boolean", nullable=false)
     */
    private $notation;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="text", length=65535, nullable=false)
     */
    private $texte;


}

