<?php

namespace Coa\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksCountryname
 *
 * @ORM\Table(name="inducks_countryname", indexes={@ORM\Index(name="fk_inducks_countryname0", columns={"languagecode"})})
 * @ORM\Entity
 */
class InducksCountryname extends \Coa\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="countrycode", type="string", length=2, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $countrycode;

    /**
     * @var string
     *
     * @ORM\Column(name="languagecode", type="string", length=5, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $languagecode;

    /**
     * @var string
     *
     * @ORM\Column(name="countryname", type="string", length=58, nullable=true)
     */
    private $countryname;



    /**
     * Set countrycode
     *
     * @param string $countrycode
     *
     * @return InducksCountryname
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
     * @return InducksCountryname
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
     * Set countryname
     *
     * @param string $countryname
     *
     * @return InducksCountryname
     */
    public function setCountryname($countryname)
    {
        $this->countryname = $countryname;

        return $this;
    }

    /**
     * Get countryname
     *
     * @return string
     */
    public function getCountryname()
    {
        return $this->countryname;
    }
}
