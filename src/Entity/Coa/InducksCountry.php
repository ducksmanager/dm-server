<?php

namespace App\Entity\Coa;

use Doctrine\ORM\Mapping as ORM;

/**
 * InducksCountry
 *
 * @ORM\Table(name="inducks_country")
 * @ORM\Entity
 */
class InducksCountry
{
    /**
     * @var string
     *
     * @ORM\Column(name="countrycode", type="string", length=2, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $countrycode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="countryname", type="string", length=20, nullable=true)
     */
    private $countryname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="defaultlanguage", type="string", length=7, nullable=true)
     */
    private $defaultlanguage;

    /**
     * @var string|null
     *
     * @ORM\Column(name="defaultmaintenanceteam", type="string", length=8, nullable=true)
     */
    private $defaultmaintenanceteam;

    public function getCountrycode(): ?string
    {
        return $this->countrycode;
    }

    public function getCountryname(): ?string
    {
        return $this->countryname;
    }

    public function setCountryname(?string $countryname): self
    {
        $this->countryname = $countryname;

        return $this;
    }

    public function getDefaultlanguage(): ?string
    {
        return $this->defaultlanguage;
    }

    public function setDefaultlanguage(?string $defaultlanguage): self
    {
        $this->defaultlanguage = $defaultlanguage;

        return $this;
    }

    public function getDefaultmaintenanceteam(): ?string
    {
        return $this->defaultmaintenanceteam;
    }

    public function setDefaultmaintenanceteam(?string $defaultmaintenanceteam): self
    {
        $this->defaultmaintenanceteam = $defaultmaintenanceteam;

        return $this;
    }


}
