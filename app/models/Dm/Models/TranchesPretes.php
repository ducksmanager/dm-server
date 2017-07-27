<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;

/**
 * TranchesPretes
 *
 * @ORM\Table(name="tranches_pretes")
 * @ORM\Entity
 * @HasLifecycleCallbacks
 */
class TranchesPretes extends \Dm\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateajout", type="date", nullable=false)
     */
    private $dateajout;

    /** @PrePersist */
    public function setDateOnPrePersist()
    {
        if (is_null($this->dateajout)) {
            $this->dateajout = new \DateTime();
        }
    }


    /**
     * Set publicationcode
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
     * Get publicationcode
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuenumber
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
     * Get issuenumber
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set dateajout
     *
     * @param \DateTime $dateajout
     *
     * @return TranchesPretes
     */
    public function setDateajout($dateajout)
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    /**
     * Get dateajout
     *
     * @return \DateTime
     */
    public function getDateajout()
    {
        return $this->dateajout;
    }
}
