<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demo
 *
 * @ORM\Table(name="demo")
 * @ORM\Entity
 */
class Demo extends \Dm\Models\BaseModel
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
     * Get datedernierinit.
     *
     * @return \DateTime
     */
    public function getDatedernierinit()
    {
        return $this->datedernierinit;
    }
}
