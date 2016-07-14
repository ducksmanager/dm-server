<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuteursPseudos
 *
 * @ORM\Table(name="auteurs_pseudos")
 * @ORM\Entity
 */
class AuteursPseudos
{
    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteur", type="string", length=50, nullable=false)
     */
    private $nomauteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteurAbrege", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomauteurabrege;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_user", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="NbNonPossedesFrance", type="integer", nullable=false)
     */
    private $nbnonpossedesfrance = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="NbNonPossedesEtranger", type="integer", nullable=false)
     */
    private $nbnonpossedesetranger = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="NbPossedes", type="integer", nullable=false)
     */
    private $nbpossedes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateStat", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $datestat = '0000-00-00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="Notation", type="boolean", nullable=false)
     */
    private $notation = '-1';


}

