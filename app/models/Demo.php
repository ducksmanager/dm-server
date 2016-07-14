<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demo
 *
 * @ORM\Table(name="demo")
 * @ORM\Entity
 */
class Demo
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateDernierInit", type="datetime", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $datedernierinit;


}

