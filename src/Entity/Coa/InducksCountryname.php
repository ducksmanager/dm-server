<?php

namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksCountryname
 *
 * @ORM\Table(name="inducks_countryname", indexes={@ORM\Index(name="fk_inducks_countryname0", columns={"languagecode"})})
 * @ORM\Entity
 */
class InducksCountryname
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
     * @var string|null
     *
     * @ORM\Column(name="countryname", type="string", length=56, nullable=true)
     */
    private $countryname;

    public function setCountrycode($countrycode): InducksCountryname
    {
        $this->countrycode = $countrycode;

        return $this;
    }

    public function getCountrycode(): string
    {
        return $this->countrycode;
    }

    public function setLanguagecode($languagecode): InducksCountryname
    {
        $this->languagecode = $languagecode;

        return $this;
    }

    public function getLanguagecode(): string
    {
        return $this->languagecode;
    }

    public function setCountryname($countryname = null): InducksCountryname
    {
        $this->countryname = $countryname;

        return $this;
    }

    public function getCountryname(): ?string
    {
        return $this->countryname;
    }
}
