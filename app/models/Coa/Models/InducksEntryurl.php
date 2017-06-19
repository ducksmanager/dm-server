<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntryurl
 *
 * @ORM\Table(name="inducks_entryurl", indexes={@ORM\Index(name="inducks_entry_fk_entrycode", columns={"entrycode"}), @ORM\Index(name="inducks_entry_fk_sitecode", columns={"sitecode"}), @ORM\Index(name="inducks_entry_fk_url", columns={"url"})})
 * @ORM\Entity
 */
class InducksEntryurl extends \Coa\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="entrycode", type="string", length=21, nullable=true)
     */
    private $entrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="sitecode", type="string", length=11, nullable=true)
     */
    private $sitecode;

    /**
     * @var integer
     *
     * @ORM\Column(name="pagenumber", type="integer", nullable=true)
     */
    private $pagenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=87, nullable=true)
     */
    private $url;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return InducksEntryurl
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntrycode()
    {
        return $this->entrycode;
    }

    /**
     * @param string $entrycode
     * @return InducksEntryurl
     */
    public function setEntrycode($entrycode)
    {
        $this->entrycode = $entrycode;
        return $this;
    }

    /**
     * @return string
     */
    public function getSitecode()
    {
        return $this->sitecode;
    }

    /**
     * @param string $sitecode
     * @return InducksEntryurl
     */
    public function setSitecode($sitecode)
    {
        $this->sitecode = $sitecode;
        return $this;
    }

    /**
     * @return int
     */
    public function getPagenumber()
    {
        return $this->pagenumber;
    }

    /**
     * @param int $pagenumber
     * @return InducksEntryurl
     */
    public function setPagenumber($pagenumber)
    {
        $this->pagenumber = $pagenumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return InducksEntryurl
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
}

