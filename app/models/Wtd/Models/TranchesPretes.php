<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretes
 *
 * @ORM\Table(name="tranches_pretes")
 * @ORM\Entity
 */
class TranchesPretes extends \Wtd\Models\BaseModel
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
     * @var string
     *
     * @ORM\Column(name="photographes", type="text", length=65535, nullable=true)
     */
    private $photographes;

    /**
     * @var string
     *
     * @ORM\Column(name="createurs", type="text", length=65535, nullable=true)
     */
    private $createurs;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateajout", type="datetime", nullable=false)
     */
    private $dateajout = 'CURRENT_TIMESTAMP';



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
     * Set photographes
     *
     * @param string $photographes
     *
     * @return TranchesPretes
     */
    public function setPhotographes($photographes)
    {
        $this->photographes = $photographes;

        return $this;
    }

    /**
     * Get photographes
     *
     * @return string
     */
    public function getPhotographes()
    {
        return $this->photographes;
    }

    /**
     * Set createurs
     *
     * @param string $createurs
     *
     * @return TranchesPretes
     */
    public function setCreateurs($createurs)
    {
        $this->createurs = $createurs;

        return $this;
    }

    /**
     * Get createurs
     *
     * @return string
     */
    public function getCreateurs()
    {
        return $this->createurs;
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
