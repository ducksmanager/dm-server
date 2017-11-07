<?php

namespace CoverId\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Covers
 *
 * @ORM\Table(name="covers", uniqueConstraints={@ORM\UniqueConstraint(name="uniquefieldset_covers", columns={"issuecode", "url"})})
 * @ORM\Entity
 */
class Covers extends \CoverId\Models\BaseModel
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
     * @ORM\Column(name="issuecode", type="string", length=17, nullable=false)
     */
    private $issuecode;

    /**
     * @var string
     *
     * @ORM\Column(name="sitecode", type="string", length=11, nullable=false)
     */
    private $sitecode;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=98, nullable=false)
     */
    private $url;



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
     * Set issuecode
     *
     * @param string $issuecode
     *
     * @return Covers
     */
    public function setIssuecode($issuecode)
    {
        $this->issuecode = $issuecode;

        return $this;
    }

    /**
     * Get issuecode
     *
     * @return string
     */
    public function getIssuecode()
    {
        return $this->issuecode;
    }

    /**
     * Set sitecode
     *
     * @param string $sitecode
     *
     * @return Covers
     */
    public function setSitecode($sitecode)
    {
        $this->sitecode = $sitecode;

        return $this;
    }

    /**
     * Get issuecode
     *
     * @return string
     */
    public function getSitecode()
    {
        return $this->sitecode;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Covers
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
