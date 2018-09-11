<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPerson
 *
 * @ORM\Table(name="inducks_person", indexes={@ORM\Index(name="fk_inducks_person0", columns={"nationalitycountrycode"}), @ORM\Index(name="fulltext_inducks_person", columns={"fullname", "birthname"})})
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
    private $personcode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nationalitycountrycode", type="string", length=2, nullable=true)
     */
    private $nationalitycountrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fullname", type="string", length=79, nullable=true)
     */
    private $fullname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="official", type="string", length=0, nullable=true)
     */
    private $official;

    /**
     * @var string|null
     *
     * @ORM\Column(name="personcomment", type="string", length=221, nullable=true)
     */
    private $personcomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unknownstudiomember", type="string", length=0, nullable=true)
     */
    private $unknownstudiomember;

    /**
     * @var string|null
     *
     * @ORM\Column(name="isfake", type="string", length=0, nullable=true)
     */
    private $isfake;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numberofindexedissues", type="integer", nullable=true)
     */
    private $numberofindexedissues;

    /**
     * @var string|null
     *
     * @ORM\Column(name="birthname", type="string", length=37, nullable=true)
     */
    private $birthname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="borndate", type="string", length=10, nullable=true)
     */
    private $borndate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bornplace", type="string", length=30, nullable=true)
     */
    private $bornplace;

    /**
     * @var string|null
     *
     * @ORM\Column(name="deceaseddate", type="string", length=10, nullable=true)
     */
    private $deceaseddate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="deceasedplace", type="string", length=31, nullable=true)
     */
    private $deceasedplace;

    /**
     * @var string|null
     *
     * @ORM\Column(name="education", type="string", length=189, nullable=true)
     */
    private $education;

    /**
     * @var string|null
     *
     * @ORM\Column(name="moviestext", type="string", length=879, nullable=true)
     */
    private $moviestext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comicstext", type="string", length=927, nullable=true)
     */
    private $comicstext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="othertext", type="string", length=307, nullable=true)
     */
    private $othertext;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photofilename", type="string", length=32, nullable=true)
     */
    private $photofilename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photocomment", type="string", length=68, nullable=true)
     */
    private $photocomment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photosource", type="string", length=67, nullable=true)
     */
    private $photosource;

    /**
     * @var string|null
     *
     * @ORM\Column(name="personrefs", type="string", length=180, nullable=true)
     */
    private $personrefs;



    /**
     * Set personcode.
     *
     * @param string|null $personcode
     *
     * @return InducksPerson
     */
    public function setPersoncode($personcode = null)
    {
        $this->personcode = $personcode;

        return $this;
    }

    /**
     * Get personcode.
     *
     * @return string
     */
    public function getPersoncode()
    {
        return $this->personcode;
    }

    /**
     * Set nationalitycountrycode.
     *
     * @param string|null $nationalitycountrycode
     *
     * @return InducksPerson
     */
    public function setNationalitycountrycode($nationalitycountrycode = null)
    {
        $this->nationalitycountrycode = $nationalitycountrycode;

        return $this;
    }

    /**
     * Get nationalitycountrycode.
     *
     * @return string|null
     */
    public function getNationalitycountrycode()
    {
        return $this->nationalitycountrycode;
    }

    /**
     * Set fullname.
     *
     * @param string|null $fullname
     *
     * @return InducksPerson
     */
    public function setFullname($fullname = null)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname.
     *
     * @return string|null
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set official.
     *
     * @param string|null $official
     *
     * @return InducksPerson
     */
    public function setOfficial($official = null)
    {
        $this->official = $official;

        return $this;
    }

    /**
     * Get official.
     *
     * @return string|null
     */
    public function getOfficial()
    {
        return $this->official;
    }

    /**
     * Set personcomment.
     *
     * @param string|null $personcomment
     *
     * @return InducksPerson
     */
    public function setPersoncomment($personcomment = null)
    {
        $this->personcomment = $personcomment;

        return $this;
    }

    /**
     * Get personcomment.
     *
     * @return string|null
     */
    public function getPersoncomment()
    {
        return $this->personcomment;
    }

    /**
     * Set unknownstudiomember.
     *
     * @param string|null $unknownstudiomember
     *
     * @return InducksPerson
     */
    public function setUnknownstudiomember($unknownstudiomember = null)
    {
        $this->unknownstudiomember = $unknownstudiomember;

        return $this;
    }

    /**
     * Get unknownstudiomember.
     *
     * @return string|null
     */
    public function getUnknownstudiomember()
    {
        return $this->unknownstudiomember;
    }

    /**
     * Set isfake.
     *
     * @param string|null $isfake
     *
     * @return InducksPerson
     */
    public function setIsfake($isfake = null)
    {
        $this->isfake = $isfake;

        return $this;
    }

    /**
     * Get isfake.
     *
     * @return string|null
     */
    public function getIsfake()
    {
        return $this->isfake;
    }

    /**
     * Set numberofindexedissues.
     *
     * @param int|null $numberofindexedissues
     *
     * @return InducksPerson
     */
    public function setNumberofindexedissues($numberofindexedissues = null)
    {
        $this->numberofindexedissues = $numberofindexedissues;

        return $this;
    }

    /**
     * Get numberofindexedissues.
     *
     * @return int|null
     */
    public function getNumberofindexedissues()
    {
        return $this->numberofindexedissues;
    }

    /**
     * Set birthname.
     *
     * @param string|null $birthname
     *
     * @return InducksPerson
     */
    public function setBirthname($birthname = null)
    {
        $this->birthname = $birthname;

        return $this;
    }

    /**
     * Get birthname.
     *
     * @return string|null
     */
    public function getBirthname()
    {
        return $this->birthname;
    }

    /**
     * Set borndate.
     *
     * @param string|null $borndate
     *
     * @return InducksPerson
     */
    public function setBorndate($borndate = null)
    {
        $this->borndate = $borndate;

        return $this;
    }

    /**
     * Get borndate.
     *
     * @return string|null
     */
    public function getBorndate()
    {
        return $this->borndate;
    }

    /**
     * Set bornplace.
     *
     * @param string|null $bornplace
     *
     * @return InducksPerson
     */
    public function setBornplace($bornplace = null)
    {
        $this->bornplace = $bornplace;

        return $this;
    }

    /**
     * Get bornplace.
     *
     * @return string|null
     */
    public function getBornplace()
    {
        return $this->bornplace;
    }

    /**
     * Set deceaseddate.
     *
     * @param string|null $deceaseddate
     *
     * @return InducksPerson
     */
    public function setDeceaseddate($deceaseddate = null)
    {
        $this->deceaseddate = $deceaseddate;

        return $this;
    }

    /**
     * Get deceaseddate.
     *
     * @return string|null
     */
    public function getDeceaseddate()
    {
        return $this->deceaseddate;
    }

    /**
     * Set deceasedplace.
     *
     * @param string|null $deceasedplace
     *
     * @return InducksPerson
     */
    public function setDeceasedplace($deceasedplace = null)
    {
        $this->deceasedplace = $deceasedplace;

        return $this;
    }

    /**
     * Get deceasedplace.
     *
     * @return string|null
     */
    public function getDeceasedplace()
    {
        return $this->deceasedplace;
    }

    /**
     * Set education.
     *
     * @param string|null $education
     *
     * @return InducksPerson
     */
    public function setEducation($education = null)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education.
     *
     * @return string|null
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set moviestext.
     *
     * @param string|null $moviestext
     *
     * @return InducksPerson
     */
    public function setMoviestext($moviestext = null)
    {
        $this->moviestext = $moviestext;

        return $this;
    }

    /**
     * Get moviestext.
     *
     * @return string|null
     */
    public function getMoviestext()
    {
        return $this->moviestext;
    }

    /**
     * Set comicstext.
     *
     * @param string|null $comicstext
     *
     * @return InducksPerson
     */
    public function setComicstext($comicstext = null)
    {
        $this->comicstext = $comicstext;

        return $this;
    }

    /**
     * Get comicstext.
     *
     * @return string|null
     */
    public function getComicstext()
    {
        return $this->comicstext;
    }

    /**
     * Set othertext.
     *
     * @param string|null $othertext
     *
     * @return InducksPerson
     */
    public function setOthertext($othertext = null)
    {
        $this->othertext = $othertext;

        return $this;
    }

    /**
     * Get othertext.
     *
     * @return string|null
     */
    public function getOthertext()
    {
        return $this->othertext;
    }

    /**
     * Set photofilename.
     *
     * @param string|null $photofilename
     *
     * @return InducksPerson
     */
    public function setPhotofilename($photofilename = null)
    {
        $this->photofilename = $photofilename;

        return $this;
    }

    /**
     * Get photofilename.
     *
     * @return string|null
     */
    public function getPhotofilename()
    {
        return $this->photofilename;
    }

    /**
     * Set photocomment.
     *
     * @param string|null $photocomment
     *
     * @return InducksPerson
     */
    public function setPhotocomment($photocomment = null)
    {
        $this->photocomment = $photocomment;

        return $this;
    }

    /**
     * Get photocomment.
     *
     * @return string|null
     */
    public function getPhotocomment()
    {
        return $this->photocomment;
    }

    /**
     * Set photosource.
     *
     * @param string|null $photosource
     *
     * @return InducksPerson
     */
    public function setPhotosource($photosource = null)
    {
        $this->photosource = $photosource;

        return $this;
    }

    /**
     * Get photosource.
     *
     * @return string|null
     */
    public function getPhotosource()
    {
        return $this->photosource;
    }

    /**
     * Set personrefs.
     *
     * @param string|null $personrefs
     *
     * @return InducksPerson
     */
    public function setPersonrefs($personrefs = null)
    {
        $this->personrefs = $personrefs;

        return $this;
    }

    /**
     * Get personrefs.
     *
     * @return string|null
     */
    public function getPersonrefs()
    {
        return $this->personrefs;
    }
}
