<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bouquineries
 *
 * @ORM\Table(name="bouquineries")
 * @ORM\Entity
 */
class Bouquineries extends \Wtd\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=25, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse", type="text", length=65535, nullable=false)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="AdresseComplete", type="text", length=65535, nullable=false)
     */
    private $adressecomplete;

    /**
     * @var integer
     *
     * @ORM\Column(name="CodePostal", type="integer", nullable=false)
     */
    private $codepostal;

    /**
     * @var string
     *
     * @ORM\Column(name="Ville", type="string", length=20, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=20, nullable=false)
     */
    private $pays = 'France';

    /**
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=false)
     */
    private $commentaire;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=true)
     */
    private $idUtilisateur;

    /**
     * @var float
     *
     * @ORM\Column(name="CoordX", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordx = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="CoordY", type="float", precision=10, scale=0, nullable=false)
     */
    private $coordy = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAjout", type="datetime", nullable=false)
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var boolean
     *
     * @ORM\Column(name="Actif", type="boolean", nullable=false)
     */
    private $actif = '1';


}

