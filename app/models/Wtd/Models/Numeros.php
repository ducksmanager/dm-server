<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numeros
 *
 * @ORM\Table(name="numeros", uniqueConstraints={@ORM\UniqueConstraint(name="Details_Numero", columns={"Pays", "Magazine", "Numero", "ID_Utilisateur"})})
 * @ORM\Entity
 */
class Numeros extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     */
    private $magazine;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", nullable=false)
     */
    private $etat;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false)
     */
    private $idAcquisition = '-1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="AV", type="boolean", nullable=false)
     */
    private $av;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     */
    private $idUtilisateur;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAjout", type="datetime", nullable=false)
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

