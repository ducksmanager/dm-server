<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPublication
 *
 * @ORM\Table(name="inducks_publication", indexes={@ORM\Index(name="fk_inducks_publication0", columns={"countrycode"}), @ORM\Index(name="fk_inducks_publication1", columns={"languagecode"}), @ORM\Index(name="fulltext_inducks_publication", columns={"title"})})
 * @ORM\Entity
 */
class InducksPublication
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="countrycode", type="string", length=2, nullable=true)
     */
    private $countrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="languagecode", type="string", length=7, nullable=true)
     */
    private $languagecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=131, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="size", type="string", length=61, nullable=true)
     */
    private $size;

    /**
     * @var string|null
     *
     * @ORM\Column(name="publicationcomment", type="string", length=1354, nullable=true)
     */
    private $publicationcomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="circulation", type="string", length=4, nullable=true)
     */
    private $circulation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numbersarefake", type="string", length=0, nullable=true)
     */
    private $numbersarefake;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=0, nullable=true)
     */
    private $error;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locked", type="string", length=0, nullable=true)
     */
    private $locked;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inxforbidden", type="string", length=0, nullable=true)
     */
    private $inxforbidden;

    /**
     * @var int|null
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=9, nullable=true)
     */
    private $maintenanceteamcode;

    public function setPublicationcode($publicationcode = null): InducksPublication
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    public function getPublicationcode(): string
    {
        return $this->publicationcode;
    }

    public function setCountrycode($countrycode = null): InducksPublication
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    public function getCountrycode(): ?string
    {
        return $this->countrycode;
    }

    public function setLanguagecode($languagecode = null): InducksPublication
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    public function getLanguagecode(): ?string
    {
        return $this->languagecode;
    }

    public function setTitle($title = null): InducksPublication
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSize($size = null): InducksPublication
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setPublicationcomment($publicationcomment = null): InducksPublication
    {
        $this->publicationcomment = $publicationcomment;

        return $this;
    }

    public function getPublicationcomment(): ?string
    {
        return $this->publicationcomment;
    }

    public function setCirculation($circulation = null): InducksPublication
    {
        $this->circulation = $circulation;

        return $this;
    }

    public function getCirculation(): ?string
    {
        return $this->circulation;
    }

    public function setNumbersarefake($numbersarefake = null): InducksPublication
    {
        $this->numbersarefake = $numbersarefake;

        return $this;
    }

    public function getNumbersarefake(): ?string
    {
        return $this->numbersarefake;
    }

    public function setError($error = null): InducksPublication
    {
        $this->error = $error;

        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setLocked($locked = null): InducksPublication
    {
        $this->locked = $locked;

        return $this;
    }

    public function getLocked(): ?string
    {
        return $this->locked;
    }

    public function setInxforbidden($inxforbidden = null): InducksPublication
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    public function getInxforbidden(): ?string
    {
        return $this->inxforbidden;
    }

    public function setInputfilecode($inputfilecode = null): InducksPublication
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    public function getInputfilecode(): ?int
    {
        return $this->inputfilecode;
    }

    public function setMaintenanceteamcode($maintenanceteamcode = null): InducksPublication
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    public function getMaintenanceteamcode(): ?string
    {
        return $this->maintenanceteamcode;
    }
}
