<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretes
 *
 * @ORM\Table(name="tranches_pretes", uniqueConstraints={@ORM\UniqueConstraint(name="tranchespretes_unique", columns={"publicationcode", "issuenumber"})}, indexes={@ORM\Index(name="tranches_pretes_dateajout_index", columns={"dateajout"}), @ORM\Index(name="tranches_pretes_publicationcode_issuenumber_index", columns={"publicationcode", "issuenumber"})})
 * @ORM\Entity
 */
class TranchesPretes extends \Dm\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     */
    private $publicationcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=10, nullable=false)
     */
    private $issuenumber = '';

    /**
     * @var int
     *
     * @ORM\Column(name="dateajout", type="integer", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var int|null
     *
     * @ORM\Column(name="points", type="integer", nullable=true)
     */
    private $points;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set publicationcode.
     *
     * @param string $publicationcode
     *
     * @return TranchesPretes
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode.
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuenumber.
     *
     * @param string $issuenumber
     *
     * @return TranchesPretes
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber.
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set dateajout.
     *
     * @param int $dateajout
     *
     * @return TranchesPretes
     */
    public function setDateajout($dateajout)
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    /**
     * Get dateajout.
     *
     * @return int
     */
    public function getDateajout()
    {
        return $this->dateajout;
    }

    /**
     * Set points.
     *
     * @param int|null $points
     *
     * @return TranchesPretes
     */
    public function setPoints($points = null)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points.
     *
     * @return int|null
     */
    public function getPoints()
    {
        return $this->points;
    }
}
