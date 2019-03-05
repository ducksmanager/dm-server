<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksEntryurl
 *
 * @ORM\Table(name="inducks_entryurl", indexes={@ORM\Index(name="fk_inducks_entryurl0", columns={"entrycode"}), @ORM\Index(name="fk_inducks_entryurl1", columns={"sitecode"}), @ORM\Index(name="fk_inducks_entryurl2", columns={"url"})})
 * @ORM\Entity
 */
class InducksEntryurl
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

    public function getId(): int
    {
        return $this->id;
    }

    public function setEntrycode($entrycode = null): InducksEntryurl
    {
        $this->entrycode = $entrycode;

        return $this;
    }

    public function getEntrycode(): ?string
    {
        return $this->entrycode;
    }

    public function setSitecode($sitecode = null): InducksEntryurl
    {
        $this->sitecode = $sitecode;

        return $this;
    }

    public function getSitecode(): ?string
    {
        return $this->sitecode;
    }

    public function setPagenumber($pagenumber = null): InducksEntryurl
    {
        $this->pagenumber = $pagenumber;

        return $this;
    }

    public function getPagenumber(): ?int
    {
        return $this->pagenumber;
    }

    public function setUrl($url = null): InducksEntryurl
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }
}
