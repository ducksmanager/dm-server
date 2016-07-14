<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParametresListes
 *
 * @ORM\Table(name="parametres_listes")
 * @ORM\Entity
 */
class ParametresListes
{
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
     * @ORM\Column(name="Type_Liste", type="string", length=20, nullable=true)
     */
    private $typeListe;

    /**
     * @var integer
     *
     * @ORM\Column(name="Position_Liste", type="integer", nullable=true)
     */
    private $positionListe;

    /**
     * @var string
     *
     * @ORM\Column(name="Parametre", type="string", length=30, nullable=true)
     */
    private $parametre;

    /**
     * @var string
     *
     * @ORM\Column(name="Valeur", type="string", length=20, nullable=true)
     */
    private $valeur;


}

