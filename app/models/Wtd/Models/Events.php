<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Events
 *
 * @ORM\Table(name="events")
 * @ORM\Entity
 */
class Events extends \Wtd\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Event", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEvent;

    /**
     * @var integer
     *
     * @ORM\Column(name="pct", type="smallint", nullable=false)
     */
    private $pct;



    /**
     * Get idEvent
     *
     * @return integer
     */
    public function getIdEvent()
    {
        return $this->idEvent;
    }

    /**
     * Set pct
     *
     * @param integer $pct
     *
     * @return Events
     */
    public function setPct($pct)
    {
        $this->pct = $pct;

        return $this;
    }

    /**
     * Get pct
     *
     * @return integer
     */
    public function getPct()
    {
        return $this->pct;
    }
}
