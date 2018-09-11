<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * TranchesEnCoursModeles
 *
 * @ORM\Table(name="tranches_en_cours_modeles", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_en_cours_modeles__numero", columns={"Pays", "Magazine", "Numero", "username"})})
 * @ORM\Entity
 */
class TranchesEnCoursModeles extends \Edgecreator\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=3, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="Magazine", type="string", length=6, nullable=false)
     */
    private $magazine;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=10, nullable=false)
     */
    private $numero;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=true)
     */
    private $username;

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="NomPhotoPrincipale", type="string", length=60, nullable=true)
     */
    private $nomphotoprincipale;

    /**
     * @var TranchesEnCoursModelesImages[]
     *
     * @OneToMany(fetch="EAGER", targetEntity="TranchesEnCoursModelesImages", cascade={"persist", "remove"}, mappedBy="idModele")
     */
    private $photos = [];

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="photographes", type="text", length=65535, nullable=true)
     */
    private $photographes;

    /**
     * @var string|null
     * @deprecated
     *
     * @ORM\Column(name="createurs", type="text", length=65535, nullable=true)
     */
    private $createurs;

    /**
     * @var TranchesEnCoursContributeurs[]
     *
     * @OneToMany(fetch="EAGER", targetEntity="TranchesEnCoursContributeurs", cascade={"persist", "remove"}, mappedBy="idModele")
     */
    private $contributeurs = [];

    /**
     * @var bool
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="PretePourPublication", type="boolean", nullable=false)
     */
    private $pretepourpublication;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pays.
     *
     * @param string $pays
     *
     * @return TranchesEnCoursModeles
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays.
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set magazine.
     *
     * @param string $magazine
     *
     * @return TranchesEnCoursModeles
     */
    public function setMagazine($magazine)
    {
        $this->magazine = $magazine;

        return $this;
    }

    /**
     * Get magazine.
     *
     * @return string
     */
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
     * Set numero.
     *
     * @param string $numero
     *
     * @return TranchesEnCoursModeles
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero.
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set username.
     *
     * @param string|null $username
     *
     * @return TranchesEnCoursModeles
     */
    public function setUsername($username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nomphotoprincipale.
     *
     * @param string|null $nomphotoprincipale
     *
     * @return TranchesEnCoursModeles
     */
    public function setNomphotoprincipale($nomphotoprincipale = null)
    {
        $this->nomphotoprincipale = $nomphotoprincipale;

        return $this;
    }

    /**
     * Get nomphotoprincipale.
     *
     * @return string|null
     */
    public function getNomphotoprincipale()
    {
        return $this->nomphotoprincipale;
    }

    /**
     * Set photos
     *
     * @param TranchesEnCoursModelesImages[] $photos
     *
     * @return TranchesEnCoursModeles
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;

        return $this;
    }

    /**
     * Get photographes
     *
     * @return TranchesEnCoursModelesImages[]
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Set photographes.
     *
     * @param string|null $photographes
     *
     * @return TranchesEnCoursModeles
     */
    public function setPhotographes($photographes = null)
    {
        $this->photographes = $photographes;

        return $this;
    }

    /**
     * Get photographes.
     *
     * @return string|null
     */
    public function getPhotographes()
    {
        return $this->photographes;
    }

    /**
     * Set createurs.
     *
     * @param string|null $createurs
     *
     * @return TranchesEnCoursModeles
     */
    public function setCreateurs($createurs = null)
    {
        $this->createurs = $createurs;

        return $this;
    }

    /**
     * Get createurs.
     *
     * @return string|null
     */
    public function getCreateurs()
    {
        return $this->createurs;
    }

    /**
     * Set contributeurs
     *
     * @param TranchesEnCoursContributeurs[] $contributeurs
     *
     * @return TranchesEnCoursModeles
     */
    public function setContributeurs($contributeurs)
    {
        $this->contributeurs = $contributeurs;

        return $this;
    }

    /**
     * Get contributeurs
     *
     * @return TranchesEnCoursContributeurs[]
     */
    public function getContributeurs()
    {
        return $this->contributeurs;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return TranchesEnCoursModeles
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set pretepourpublication.
     *
     * @param bool $pretepourpublication
     *
     * @return TranchesEnCoursModeles
     */
    public function setPretepourpublication($pretepourpublication)
    {
        $this->pretepourpublication = $pretepourpublication;

        return $this;
    }

    /**
     * Get pretepourpublication.
     *
     * @return bool
     */
    public function getPretepourpublication()
    {
        return $this->pretepourpublication;
    }
}
