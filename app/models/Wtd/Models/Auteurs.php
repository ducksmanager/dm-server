<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auteurs
 *
 * @ORM\Table(name="auteurs")
 * @ORM\Entity
 */
class Auteurs extends \Wtd\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_auteur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAuteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteur", type="string", length=20, nullable=false)
     */
    private $nomauteur;

    /**
     * @var integer
     *
     * @ORM\Column(name="NbHistoires", type="integer", nullable=false)
     */
    private $nbhistoires;

    /**
     * @var integer
     *
     * @ORM\Column(name="NbHistoires_old", type="integer", nullable=false)
     */
    private $nbhistoiresOld;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateMAJ", type="date", nullable=false)
     */
    private $datemaj;


}

