<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntryurl
 *
 * @ORM\Table(name="inducks_entryurl", indexes={@ORM\Index(name="fk_inducks_entryurl0", columns={"entrycode"}), @ORM\Index(name="fk_inducks_entryurl1", columns={"sitecode"}), @ORM\Index(name="fk_inducks_entryurl2", columns={"url"})})
 * @ORM\Entity
 */
class InducksEntryurl extends \Coa\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entrycode", type="string", length=21, nullable=true)
     */
    private $entrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="sitecode", type="string", length=11, nullable=true)
     */
    private $sitecode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pagenumber", type="integer", nullable=true)
     */
    private $pagenumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url", type="string", length=87, nullable=true)
     */
    private $url;



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
     * Set entrycode.
     *
     * @param string|null $entrycode
     *
     * @return InducksEntryurl
     */
    public function setEntrycode($entrycode = null)
    {
        $this->entrycode = $entrycode;

        return $this;
    }

    /**
     * Get entrycode.
     *
     * @return string|null
     */
    public function getEntrycode()
    {
        return $this->entrycode;
    }

    /**
     * Set sitecode.
     *
     * @param string|null $sitecode
     *
     * @return InducksEntryurl
     */
    public function setSitecode($sitecode = null)
    {
        $this->sitecode = $sitecode;

        return $this;
    }

    /**
     * Get sitecode.
     *
     * @return string|null
     */
    public function getSitecode()
    {
        return $this->sitecode;
    }

    /**
     * Set pagenumber.
     *
     * @param int|null $pagenumber
     *
     * @return InducksEntryurl
     */
    public function setPagenumber($pagenumber = null)
    {
        $this->pagenumber = $pagenumber;

        return $this;
    }

    /**
     * Get pagenumber.
     *
     * @return int|null
     */
    public function getPagenumber()
    {
        return $this->pagenumber;
    }

    /**
     * Set url.
     *
     * @param string|null $url
     *
     * @return InducksEntryurl
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }
}
