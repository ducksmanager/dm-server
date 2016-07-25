<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demo
 *
 * @ORM\Table(name="demo")
 * @ORM\Entity
 */
class Demo extends \Wtd\Models\BaseModel
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateDernierInit", type="datetime", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $datedernierinit;



    /**
     * Get datedernierinit
     *
     * @return \DateTime
     */
    public function getDatedernierinit()
    {
        return $this->datedernierinit;
    }
}
