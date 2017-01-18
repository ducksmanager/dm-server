<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPerson
 *
 * @ORM\Table(name="inducks_person", indexes={@ORM\Index(name="fk_inducks_person0", columns={"nationalitycountrycode"}), @ORM\Index(name="fullname", columns={"fullname", "birthname"})})
 * @ORM\Entity
 */
class InducksPerson extends \Coa\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="personcode", type="string", length=79, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $personcode = '';

    /**
     * @var string
     *
     * @ORM\Column(name="nationalitycountrycode", type="string", length=2, nullable=true)
     */
    private $nationalitycountrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="text", length=65535, nullable=true)
     */
    private $fullname;

    /**
     * @var string
     *
     * @ORM\Column(name="official", type="string", nullable=true)
     */
    private $official;

    /**
     * @var string
     *
     * @ORM\Column(name="personcomment", type="string", length=221, nullable=true)
     */
    private $personcomment;

    /**
     * @var string
     *
     * @ORM\Column(name="unknownstudiomember", type="string", nullable=true)
     */
    private $unknownstudiomember;

    /**
     * @var string
     *
     * @ORM\Column(name="isfake", type="string", nullable=true)
     */
    private $isfake;

    /**
     * @var string
     *
     * @ORM\Column(name="birthname", type="text", length=65535, nullable=true)
     */
    private $birthname;

    /**
     * @var string
     *
     * @ORM\Column(name="borndate", type="string", length=10, nullable=true)
     */
    private $borndate;

    /**
     * @var string
     *
     * @ORM\Column(name="bornplace", type="string", length=30, nullable=true)
     */
    private $bornplace;

    /**
     * @var string
     *
     * @ORM\Column(name="deceaseddate", type="string", length=10, nullable=true)
     */
    private $deceaseddate;

    /**
     * @var string
     *
     * @ORM\Column(name="deceasedplace", type="string", length=31, nullable=true)
     */
    private $deceasedplace;

    /**
     * @var string
     *
     * @ORM\Column(name="education", type="string", length=189, nullable=true)
     */
    private $education;

    /**
     * @var string
     *
     * @ORM\Column(name="moviestext", type="string", length=879, nullable=true)
     */
    private $moviestext;

    /**
     * @var string
     *
     * @ORM\Column(name="comicstext", type="string", length=1232, nullable=true)
     */
    private $comicstext;

    /**
     * @var string
     *
     * @ORM\Column(name="othertext", type="string", length=307, nullable=true)
     */
    private $othertext;

    /**
     * @var string
     *
     * @ORM\Column(name="photofilename", type="string", length=32, nullable=true)
     */
    private $photofilename;

    /**
     * @var string
     *
     * @ORM\Column(name="photocomment", type="string", length=68, nullable=true)
     */
    private $photocomment;

    /**
     * @var string
     *
     * @ORM\Column(name="photosource", type="string", length=67, nullable=true)
     */
    private $photosource;

    /**
     * @var string
     *
     * @ORM\Column(name="personrefs", type="string", length=180, nullable=true)
     */
    private $personrefs;



    /**
     * Get personcode
     *
     * @return string
     */
    public function getPersoncode()
    {
        return $this->personcode;
    }

    /**
     * @param string $personcode
     */
    public function setPersoncode($personcode)
    {
        $this->personcode = $personcode;
    }

    /**
     * Set nationalitycountrycode
     *
     * @param string $nationalitycountrycode
     *
     * @return InducksPerson
     */
    public function setNationalitycountrycode($nationalitycountrycode)
    {
        $this->nationalitycountrycode = $nationalitycountrycode;

        return $this;
    }

    /**
     * Get nationalitycountrycode
     *
     * @return string
     */
    public function getNationalitycountrycode()
    {
        return $this->nationalitycountrycode;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     *
     * @return InducksPerson
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set official
     *
     * @param string $official
     *
     * @return InducksPerson
     */
    public function setOfficial($official)
    {
        $this->official = $official;

        return $this;
    }

    /**
     * Get official
     *
     * @return string
     */
    public function getOfficial()
    {
        return $this->official;
    }

    /**
     * Set personcomment
     *
     * @param string $personcomment
     *
     * @return InducksPerson
     */
    public function setPersoncomment($personcomment)
    {
        $this->personcomment = $personcomment;

        return $this;
    }

    /**
     * Get personcomment
     *
     * @return string
     */
    public function getPersoncomment()
    {
        return $this->personcomment;
    }

    /**
     * Set unknownstudiomember
     *
     * @param string $unknownstudiomember
     *
     * @return InducksPerson
     */
    public function setUnknownstudiomember($unknownstudiomember)
    {
        $this->unknownstudiomember = $unknownstudiomember;

        return $this;
    }

    /**
     * Get unknownstudiomember
     *
     * @return string
     */
    public function getUnknownstudiomember()
    {
        return $this->unknownstudiomember;
    }

    /**
     * Set isfake
     *
     * @param string $isfake
     *
     * @return InducksPerson
     */
    public function setIsfake($isfake)
    {
        $this->isfake = $isfake;

        return $this;
    }

    /**
     * Get isfake
     *
     * @return string
     */
    public function getIsfake()
    {
        return $this->isfake;
    }

    /**
     * Set birthname
     *
     * @param string $birthname
     *
     * @return InducksPerson
     */
    public function setBirthname($birthname)
    {
        $this->birthname = $birthname;

        return $this;
    }

    /**
     * Get birthname
     *
     * @return string
     */
    public function getBirthname()
    {
        return $this->birthname;
    }

    /**
     * Set borndate
     *
     * @param string $borndate
     *
     * @return InducksPerson
     */
    public function setBorndate($borndate)
    {
        $this->borndate = $borndate;

        return $this;
    }

    /**
     * Get borndate
     *
     * @return string
     */
    public function getBorndate()
    {
        return $this->borndate;
    }

    /**
     * Set bornplace
     *
     * @param string $bornplace
     *
     * @return InducksPerson
     */
    public function setBornplace($bornplace)
    {
        $this->bornplace = $bornplace;

        return $this;
    }

    /**
     * Get bornplace
     *
     * @return string
     */
    public function getBornplace()
    {
        return $this->bornplace;
    }

    /**
     * Set deceaseddate
     *
     * @param string $deceaseddate
     *
     * @return InducksPerson
     */
    public function setDeceaseddate($deceaseddate)
    {
        $this->deceaseddate = $deceaseddate;

        return $this;
    }

    /**
     * Get deceaseddate
     *
     * @return string
     */
    public function getDeceaseddate()
    {
        return $this->deceaseddate;
    }

    /**
     * Set deceasedplace
     *
     * @param string $deceasedplace
     *
     * @return InducksPerson
     */
    public function setDeceasedplace($deceasedplace)
    {
        $this->deceasedplace = $deceasedplace;

        return $this;
    }

    /**
     * Get deceasedplace
     *
     * @return string
     */
    public function getDeceasedplace()
    {
        return $this->deceasedplace;
    }

    /**
     * Set education
     *
     * @param string $education
     *
     * @return InducksPerson
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return string
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set moviestext
     *
     * @param string $moviestext
     *
     * @return InducksPerson
     */
    public function setMoviestext($moviestext)
    {
        $this->moviestext = $moviestext;

        return $this;
    }

    /**
     * Get moviestext
     *
     * @return string
     */
    public function getMoviestext()
    {
        return $this->moviestext;
    }

    /**
     * Set comicstext
     *
     * @param string $comicstext
     *
     * @return InducksPerson
     */
    public function setComicstext($comicstext)
    {
        $this->comicstext = $comicstext;

        return $this;
    }

    /**
     * Get comicstext
     *
     * @return string
     */
    public function getComicstext()
    {
        return $this->comicstext;
    }

    /**
     * Set othertext
     *
     * @param string $othertext
     *
     * @return InducksPerson
     */
    public function setOthertext($othertext)
    {
        $this->othertext = $othertext;

        return $this;
    }

    /**
     * Get othertext
     *
     * @return string
     */
    public function getOthertext()
    {
        return $this->othertext;
    }

    /**
     * Set photofilename
     *
     * @param string $photofilename
     *
     * @return InducksPerson
     */
    public function setPhotofilename($photofilename)
    {
        $this->photofilename = $photofilename;

        return $this;
    }

    /**
     * Get photofilename
     *
     * @return string
     */
    public function getPhotofilename()
    {
        return $this->photofilename;
    }

    /**
     * Set photocomment
     *
     * @param string $photocomment
     *
     * @return InducksPerson
     */
    public function setPhotocomment($photocomment)
    {
        $this->photocomment = $photocomment;

        return $this;
    }

    /**
     * Get photocomment
     *
     * @return string
     */
    public function getPhotocomment()
    {
        return $this->photocomment;
    }

    /**
     * Set photosource
     *
     * @param string $photosource
     *
     * @return InducksPerson
     */
    public function setPhotosource($photosource)
    {
        $this->photosource = $photosource;

        return $this;
    }

    /**
     * Get photosource
     *
     * @return string
     */
    public function getPhotosource()
    {
        return $this->photosource;
    }

    /**
     * Set personrefs
     *
     * @param string $personrefs
     *
     * @return InducksPerson
     */
    public function setPersonrefs($personrefs)
    {
        $this->personrefs = $personrefs;

        return $this;
    }

    /**
     * Get personrefs
     *
     * @return string
     */
    public function getPersonrefs()
    {
        return $this->personrefs;
    }
}
