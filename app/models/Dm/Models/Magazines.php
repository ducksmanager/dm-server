<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magazines
 *
 * @ORM\Table(name="magazines")
 * @ORM\Entity
 * @deprecated
 */
class Magazines extends \Dm\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="PaysAbrege", type="string", length=4, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $paysabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAbrege", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nomabrege;

    /**
     * @var string
     *
     * @ORM\Column(name="NomComplet", type="string", length=70, nullable=false)
     */
    private $nomcomplet;

    /**
     * @var string
     *
     * @ORM\Column(name="RedirigeDepuis", type="string", length=7, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $redirigedepuis;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="NeParaitPlus", type="boolean", nullable=true)
     */
    private $neparaitplus;



    /**
     * Set paysabrege.
     *
     * @param string $paysabrege
     *
     * @return Magazines
     */
    public function setPaysabrege($paysabrege)
    {
        $this->paysabrege = $paysabrege;

        return $this;
    }

    /**
     * Get paysabrege.
     *
     * @return string
     */
    public function getPaysabrege()
    {
        return $this->paysabrege;
    }

    /**
     * Set nomabrege.
     *
     * @param string $nomabrege
     *
     * @return Magazines
     */
    public function setNomabrege($nomabrege)
    {
        $this->nomabrege = $nomabrege;

        return $this;
    }

    /**
     * Get nomabrege.
     *
     * @return string
     */
    public function getNomabrege()
    {
        return $this->nomabrege;
    }

    /**
     * Set nomcomplet.
     *
     * @param string $nomcomplet
     *
     * @return Magazines
     */
    public function setNomcomplet($nomcomplet)
    {
        $this->nomcomplet = $nomcomplet;

        return $this;
    }

    /**
     * Get nomcomplet.
     *
     * @return string
     */
    public function getNomcomplet()
    {
        return $this->nomcomplet;
    }

    /**
     * Set redirigedepuis.
     *
     * @param string $redirigedepuis
     *
     * @return Magazines
     */
    public function setRedirigedepuis($redirigedepuis)
    {
        $this->redirigedepuis = $redirigedepuis;

        return $this;
    }

    /**
     * Get redirigedepuis.
     *
     * @return string
     */
    public function getRedirigedepuis()
    {
        return $this->redirigedepuis;
    }

    /**
     * Set neparaitplus.
     *
     * @param bool|null $neparaitplus
     *
     * @return Magazines
     */
    public function setNeparaitplus($neparaitplus = null)
    {
        $this->neparaitplus = $neparaitplus;

        return $this;
    }

    /**
     * Get neparaitplus.
     *
     * @return bool|null
     */
    public function getNeparaitplus()
    {
        return $this->neparaitplus;
    }
}
