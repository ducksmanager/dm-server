<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPreviews
 *
 * @ORM\Table(name="tranches_previews")
 * @ORM\Entity
 */
class TranchesPreviews extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="ID_Session", type="string", length=32, nullable=true)
     */
    private $idSession;

    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Preview", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPreview;

    /**
     * @var string
     *
     * @ORM\Column(name="Options", type="string", length=2000, nullable=true)
     */
    private $options = '0';


}

