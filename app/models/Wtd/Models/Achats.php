<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Achats
 *
 * @ORM\Table(name="achats")
 * @ORM\Entity
 */
class Achats extends \Wtd\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idAcquisition;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_User", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Date", type="date", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_couleur", type="string", length=9, nullable=true)
     */
    private $styleCouleur;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_soulignement", type="string", nullable=true)
     */
    private $styleSoulignement;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_entourage", type="string", nullable=true)
     */
    private $styleEntourage;

    /**
     * @var string
     *
     * @ORM\Column(name="Style_marquage", type="string", nullable=true)
     */
    private $styleMarquage;

    /**
     * @var string
     *
     * @ORM\Column(name="Description", type="string", length=100, nullable=false)
     */
    private $description;


}

