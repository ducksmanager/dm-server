<?php
namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksPerson
 *
 * @ORM\Table(name="inducks_person", indexes={@ORM\Index(name="fk_inducks_person0", columns={"nationalitycountrycode"}), @ORM\Index(name="fulltext_inducks_person", columns={"fullname", "birthname"})})
 * @ORM\Entity
 */
class InducksPerson
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

    public function setPersoncode($personcode = null): InducksPerson
    {
        $this->personcode = $personcode;

        return $this;
    }

    public function getPersoncode(): string
    {
        return $this->personcode;
    }

    public function setNationalitycountrycode($nationalitycountrycode = null): InducksPerson
    {
        $this->nationalitycountrycode = $nationalitycountrycode;

        return $this;
    }

    public function getNationalitycountrycode(): ?string
    {
        return $this->nationalitycountrycode;
    }

    public function setFullname($fullname = null): InducksPerson
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setOfficial($official = null): InducksPerson
    {
        $this->official = $official;

        return $this;
    }

    public function getOfficial(): ?string
    {
        return $this->official;
    }

    public function setPersoncomment($personcomment = null): InducksPerson
    {
        $this->personcomment = $personcomment;

        return $this;
    }

    public function getPersoncomment(): ?string
    {
        return $this->personcomment;
    }

    public function setUnknownstudiomember($unknownstudiomember = null): InducksPerson
    {
        $this->unknownstudiomember = $unknownstudiomember;

        return $this;
    }

    public function getUnknownstudiomember(): ?string
    {
        return $this->unknownstudiomember;
    }

    public function setIsfake($isfake = null): InducksPerson
    {
        $this->isfake = $isfake;

        return $this;
    }

    public function getIsfake(): ?string
    {
        return $this->isfake;
    }

    public function setNumberofindexedissues($numberofindexedissues = null): InducksPerson
    {
        $this->numberofindexedissues = $numberofindexedissues;

        return $this;
    }

    public function getNumberofindexedissues(): ?int
    {
        return $this->numberofindexedissues;
    }

    public function setBirthname($birthname = null): InducksPerson
    {
        $this->birthname = $birthname;

        return $this;
    }

    public function getBirthname(): ?string
    {
        return $this->birthname;
    }

    public function setBorndate($borndate = null): InducksPerson
    {
        $this->borndate = $borndate;

        return $this;
    }

    public function getBorndate(): ?string
    {
        return $this->borndate;
    }

    public function setBornplace($bornplace = null): InducksPerson
    {
        $this->bornplace = $bornplace;

        return $this;
    }

    public function getBornplace(): ?string
    {
        return $this->bornplace;
    }

    public function setDeceaseddate($deceaseddate = null): InducksPerson
    {
        $this->deceaseddate = $deceaseddate;

        return $this;
    }

    public function getDeceaseddate(): ?string
    {
        return $this->deceaseddate;
    }

    public function setDeceasedplace($deceasedplace = null): InducksPerson
    {
        $this->deceasedplace = $deceasedplace;

        return $this;
    }

    public function getDeceasedplace(): ?string
    {
        return $this->deceasedplace;
    }

    public function setEducation($education = null): InducksPerson
    {
        $this->education = $education;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setMoviestext($moviestext = null): InducksPerson
    {
        $this->moviestext = $moviestext;

        return $this;
    }

    public function getMoviestext(): ?string
    {
        return $this->moviestext;
    }

    public function setComicstext($comicstext = null): InducksPerson
    {
        $this->comicstext = $comicstext;

        return $this;
    }

    public function getComicstext(): ?string
    {
        return $this->comicstext;
    }

    public function setOthertext($othertext = null): InducksPerson
    {
        $this->othertext = $othertext;

        return $this;
    }

    public function getOthertext(): ?string
    {
        return $this->othertext;
    }

    public function setPhotofilename($photofilename = null): InducksPerson
    {
        $this->photofilename = $photofilename;

        return $this;
    }

    public function getPhotofilename(): ?string
    {
        return $this->photofilename;
    }

    public function setPhotocomment($photocomment = null): InducksPerson
    {
        $this->photocomment = $photocomment;

        return $this;
    }

    public function getPhotocomment(): ?string
    {
        return $this->photocomment;
    }

    public function setPhotosource($photosource = null): InducksPerson
    {
        $this->photosource = $photosource;

        return $this;
    }

    public function getPhotosource(): ?string
    {
        return $this->photosource;
    }

    public function setPersonrefs($personrefs = null): InducksPerson
    {
        $this->personrefs = $personrefs;

        return $this;
    }

    public function getPersonrefs(): ?string
    {
        return $this->personrefs;
    }
}
