<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesMyfonts
 *
 * @ORM\Table(name="images_myfonts")
 * @ORM\Entity
 */
class ImagesMyfonts extends \Wtd\Models\BaseModel
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
     * @ORM\Column(name="Font", type="string", length=150, nullable=true)
     */
    private $font;

    /**
     * @var string
     *
     * @ORM\Column(name="Color", type="string", length=10, nullable=true)
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="ColorBG", type="string", length=10, nullable=true)
     */
    private $colorbg;

    /**
     * @var string
     *
     * @ORM\Column(name="Width", type="string", length=7, nullable=true)
     */
    private $width;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="string", length=150, nullable=true)
     */
    private $texte;

    /**
     * @var string
     *
     * @ORM\Column(name="Precision_", type="string", length=5, nullable=true)
     */
    private $precision;


}

