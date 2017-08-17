<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\OneToMany;
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateajout", type="datetime", nullable=false)
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
     * @var TranchesDoublons[]
     *
     * @OneToMany(fetch="EAGER", targetEntity="TranchesDoublons", mappedBy="tranchereference")
     */
    private $doublons;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return TranchesDoublons[]
     */
    public function getDoublons()
    {
        return $this->doublons;
    }

    /**
     * @param TranchesDoublons[] $doublons
     * @return $this
     */
    public function setDoublons($doublons)
    {
        $this->doublons = $doublons;

        return $this;
    }
}
