<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPublication
 *
 * @ORM\Table(name="inducks_publication", indexes={@ORM\Index(name="fk_inducks_publication0", columns={"countrycode"}), @ORM\Index(name="fk_inducks_publication1", columns={"languagecode"}), @ORM\Index(name="fulltext_inducks_publication", columns={"title"})})
 * @ORM\Entity
 */
class InducksPublication extends \Coa\Models\BaseModel
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



    /**
     * Set publicationcode.
     *
     * @param string|null $publicationcode
     *
     * @return InducksPublication
     */
    public function setPublicationcode($publicationcode = null)
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
     * Set countrycode.
     *
     * @param string|null $countrycode
     *
     * @return InducksPublication
     */
    public function setCountrycode($countrycode = null)
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    /**
     * Get countrycode.
     *
     * @return string|null
     */
    public function getCountrycode()
    {
        return $this->countrycode;
    }

    /**
     * Set languagecode.
     *
     * @param string|null $languagecode
     *
     * @return InducksPublication
     */
    public function setLanguagecode($languagecode = null)
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    /**
     * Get languagecode.
     *
     * @return string|null
     */
    public function getLanguagecode()
    {
        return $this->languagecode;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return InducksPublication
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set size.
     *
     * @param string|null $size
     *
     * @return InducksPublication
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size.
     *
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set publicationcomment.
     *
     * @param string|null $publicationcomment
     *
     * @return InducksPublication
     */
    public function setPublicationcomment($publicationcomment = null)
    {
        $this->publicationcomment = $publicationcomment;

        return $this;
    }

    /**
     * Get publicationcomment.
     *
     * @return string|null
     */
    public function getPublicationcomment()
    {
        return $this->publicationcomment;
    }

    /**
     * Set circulation.
     *
     * @param string|null $circulation
     *
     * @return InducksPublication
     */
    public function setCirculation($circulation = null)
    {
        $this->circulation = $circulation;

        return $this;
    }

    /**
     * Get circulation.
     *
     * @return string|null
     */
    public function getCirculation()
    {
        return $this->circulation;
    }

    /**
     * Set numbersarefake.
     *
     * @param string|null $numbersarefake
     *
     * @return InducksPublication
     */
    public function setNumbersarefake($numbersarefake = null)
    {
        $this->numbersarefake = $numbersarefake;

        return $this;
    }

    /**
     * Get numbersarefake.
     *
     * @return string|null
     */
    public function getNumbersarefake()
    {
        return $this->numbersarefake;
    }

    /**
     * Set error.
     *
     * @param string|null $error
     *
     * @return InducksPublication
     */
    public function setError($error = null)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error.
     *
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set locked.
     *
     * @param string|null $locked
     *
     * @return InducksPublication
     */
    public function setLocked($locked = null)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked.
     *
     * @return string|null
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set inxforbidden.
     *
     * @param string|null $inxforbidden
     *
     * @return InducksPublication
     */
    public function setInxforbidden($inxforbidden = null)
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    /**
     * Get inxforbidden.
     *
     * @return string|null
     */
    public function getInxforbidden()
    {
        return $this->inxforbidden;
    }

    /**
     * Set inputfilecode.
     *
     * @param int|null $inputfilecode
     *
     * @return InducksPublication
     */
    public function setInputfilecode($inputfilecode = null)
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    /**
     * Get inputfilecode.
     *
     * @return int|null
     */
    public function getInputfilecode()
    {
        return $this->inputfilecode;
    }

    /**
     * Set maintenanceteamcode.
     *
     * @param string|null $maintenanceteamcode
     *
     * @return InducksPublication
     */
    public function setMaintenanceteamcode($maintenanceteamcode = null)
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    /**
     * Get maintenanceteamcode.
     *
     * @return string|null
     */
    public function getMaintenanceteamcode()
    {
        return $this->maintenanceteamcode;
    }
}
