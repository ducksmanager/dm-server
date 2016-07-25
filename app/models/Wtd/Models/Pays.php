<?php

namespace Wtd\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pays
 *
 * @ORM\Table(name="pays")
 * @ORM\Entity
 */
class Pays extends \Wtd\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="NomAbrege", type="string", length=10, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomComplet", type="string", length=60, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomcomplet;

    /**
     * @var string
     *
     * @ORM\Column(name="L10n", type="string", length=5, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $l10n = 'fr';



    /**
     * Set nomabrege
     *
     * @param string $nomabrege
     *
     * @return Pays
     */
    public function setNomabrege($nomabrege)
    {
        $this->nomabrege = $nomabrege;

        return $this;
    }

    /**
     * Get nomabrege
     *
     * @return string
     */
    public function getNomabrege()
    {
        return $this->nomabrege;
    }

    /**
     * Set nomcomplet
     *
     * @param string $nomcomplet
     *
     * @return Pays
     */
    public function setNomcomplet($nomcomplet)
    {
        $this->nomcomplet = $nomcomplet;

        return $this;
    }

    /**
     * Get nomcomplet
     *
     * @return string
     */
    public function getNomcomplet()
    {
        return $this->nomcomplet;
    }

    /**
     * Set l10n
     *
     * @param string $l10n
     *
     * @return Pays
     */
    public function setL10n($l10n)
    {
        $this->l10n = $l10n;

        return $this;
    }

    /**
     * Get l10n
     *
     * @return string
     */
    public function getL10n()
    {
        return $this->l10n;
    }
}
