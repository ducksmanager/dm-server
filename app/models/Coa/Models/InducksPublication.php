<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPublication
 *
 * @ORM\Table(name="inducks_publication", indexes={@ORM\Index(name="inducks_publication_fk0", columns={"countrycode"}), @ORM\Index(name="fk1", columns={"languagecode"}), @ORM\Index(name="title", columns={"title"})})
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
    private $publicationcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="countrycode", type="string", length=2, nullable=true)
     */
    private $countrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="languagecode", type="string", length=7, nullable=true)
     */
    private $languagecode;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=61, nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="publicationcomment", type="string", length=1298, nullable=true)
     */
    private $publicationcomment;

    /**
     * @var string
     *
     * @ORM\Column(name="circulation", type="string", length=4, nullable=true)
     */
    private $circulation;

    /**
     * @var string
     *
     * @ORM\Column(name="numbersarefake", type="string", nullable=true)
     */
    private $numbersarefake;

    /**
     * @var string
     *
     * @ORM\Column(name="error", type="string", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="locked", type="string", nullable=true)
     */
    private $locked;

    /**
     * @var string
     *
     * @ORM\Column(name="inxforbidden", type="string", nullable=true)
     */
    private $inxforbidden;

    /**
     * @var integer
     *
     * @ORM\Column(name="inputfilecode", type="integer", nullable=true)
     */
    private $inputfilecode;

    /**
     * @var string
     *
     * @ORM\Column(name="maintenanceteamcode", type="string", length=9, nullable=true)
     */
    private $maintenanceteamcode;



    /**
     * Set publicationcode
     *
     * @param string $publicationcode
     *
     * @return InducksPublication
     */
    public function setPublicationCode($publicationcode)
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
     * Set countrycode
     *
     * @param string $countrycode
     *
     * @return InducksPublication
     */
    public function setCountrycode($countrycode)
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    /**
     * Get countrycode
     *
     * @return string
     */
    public function getCountrycode()
    {
        return $this->countrycode;
    }

    /**
     * Set languagecode
     *
     * @param string $languagecode
     *
     * @return InducksPublication
     */
    public function setLanguagecode($languagecode)
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    /**
     * Get languagecode
     *
     * @return string
     */
    public function getLanguagecode()
    {
        return $this->languagecode;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return InducksPublication
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set size
     *
     * @param string $size
     *
     * @return InducksPublication
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set publicationcomment
     *
     * @param string $publicationcomment
     *
     * @return InducksPublication
     */
    public function setPublicationcomment($publicationcomment)
    {
        $this->publicationcomment = $publicationcomment;

        return $this;
    }

    /**
     * Get publicationcomment
     *
     * @return string
     */
    public function getPublicationcomment()
    {
        return $this->publicationcomment;
    }

    /**
     * Set circulation
     *
     * @param string $circulation
     *
     * @return InducksPublication
     */
    public function setCirculation($circulation)
    {
        $this->circulation = $circulation;

        return $this;
    }

    /**
     * Get circulation
     *
     * @return string
     */
    public function getCirculation()
    {
        return $this->circulation;
    }

    /**
     * Set numbersarefake
     *
     * @param string $numbersarefake
     *
     * @return InducksPublication
     */
    public function setNumbersarefake($numbersarefake)
    {
        $this->numbersarefake = $numbersarefake;

        return $this;
    }

    /**
     * Get numbersarefake
     *
     * @return string
     */
    public function getNumbersarefake()
    {
        return $this->numbersarefake;
    }

    /**
     * Set error
     *
     * @param string $error
     *
     * @return InducksPublication
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set locked
     *
     * @param string $locked
     *
     * @return InducksPublication
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set inxforbidden
     *
     * @param string $inxforbidden
     *
     * @return InducksPublication
     */
    public function setInxforbidden($inxforbidden)
    {
        $this->inxforbidden = $inxforbidden;

        return $this;
    }

    /**
     * Get inxforbidden
     *
     * @return string
     */
    public function getInxforbidden()
    {
        return $this->inxforbidden;
    }

    /**
     * Set inputfilecode
     *
     * @param integer $inputfilecode
     *
     * @return InducksPublication
     */
    public function setInputfilecode($inputfilecode)
    {
        $this->inputfilecode = $inputfilecode;

        return $this;
    }

    /**
     * Get inputfilecode
     *
     * @return integer
     */
    public function getInputfilecode()
    {
        return $this->inputfilecode;
    }

    /**
     * Set maintenanceteamcode
     *
     * @param string $maintenanceteamcode
     *
     * @return InducksPublication
     */
    public function setMaintenanceteamcode($maintenanceteamcode)
    {
        $this->maintenanceteamcode = $maintenanceteamcode;

        return $this;
    }

    /**
     * Get maintenanceteamcode
     *
     * @return string
     */
    public function getMaintenanceteamcode()
    {
        return $this->maintenanceteamcode;
    }
}
