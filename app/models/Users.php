<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=40, nullable=false)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="AccepterPartage", type="boolean", nullable=false)
     */
    private $accepterpartage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateInscription", type="date", nullable=false)
     */
    private $dateinscription = '0000-00-00';

    /**
     * @var string
     *
     * @ORM\Column(name="EMail", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="RecommandationsListeMags", type="boolean", nullable=false)
     */
    private $recommandationslistemags = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="BetaUser", type="boolean", nullable=false)
     */
    private $betauser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="AfficherVideo", type="boolean", nullable=false)
     */
    private $affichervideo = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture1", type="string", length=20, nullable=false)
     */
    private $bibliothequeTexture1 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture1", type="string", length=50, nullable=false)
     */
    private $bibliothequeSousTexture1 = 'HONDURAS MAHOGANY';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Texture2", type="string", length=20, nullable=false)
     */
    private $bibliothequeTexture2 = 'bois';

    /**
     * @var string
     *
     * @ORM\Column(name="Bibliotheque_Sous_Texture2", type="string", length=50, nullable=false)
     */
    private $bibliothequeSousTexture2 = 'KNOTTY PINE';

    /**
     * @var float
     *
     * @ORM\Column(name="Bibliotheque_Grossissement", type="float", precision=10, scale=0, nullable=false)
     */
    private $bibliothequeGrossissement = '1.5';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DernierAcces", type="datetime", nullable=false)
     */
    private $dernieracces = 'CURRENT_TIMESTAMP';


}

