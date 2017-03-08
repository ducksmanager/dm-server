<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursModeles
 *
 * @ORM\Table(name="tranches_en_cours_modeles", uniqueConstraints={@ORM\UniqueConstraint(name="tranches_en_cours_modeles__numero", columns={"Pays", "Magazine", "Numero", "username"})})
 * @ORM\Entity
 */
class TranchesEnCoursModeles extends \EdgeCreator\Models\BaseModel
{
    /**
     * @var integer
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
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=25, nullable=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="NomPhotoPrincipale", type="string", length=60, nullable=true)
     */
    private $nomphotoprincipale;

    /**
     * @var string
     *
     * @ORM\Column(name="photographes", type="text", length=65535, nullable=true)
     */
    private $photographes;

    /**
     * @var string
     *
     * @ORM\Column(name="createurs", type="text", length=65535, nullable=true)
     */
    private $createurs;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Active", type="boolean", nullable=false)
     */
    private $active = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="PretePourPublication", type="boolean", nullable=false)
     */
    private $pretepourpublication = '0';



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pays
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
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set magazine
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
     * Get magazine
     *
     * @return string
     */
    public function getMagazine()
    {
        return $this->magazine;
    }

    /**
     * Set numero
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
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return TranchesEnCoursModeles
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set nomphotoprincipale
     *
     * @param string $nomphotoprincipale
     *
     * @return TranchesEnCoursModeles
     */
    public function setNomphotoprincipale($nomphotoprincipale)
    {
        $this->nomphotoprincipale = $nomphotoprincipale;

        return $this;
    }

    /**
     * Get nomphotoprincipale
     *
     * @return string
     */
    public function getNomphotoprincipale()
    {
        return $this->nomphotoprincipale;
    }

    /**
     * Set photographes
     *
     * @param string $photographes
     *
     * @return TranchesEnCoursModeles
     */
    public function setPhotographes($photographes)
    {
        $this->photographes = $photographes;

        return $this;
    }

    /**
     * Get photographes
     *
     * @return string
     */
    public function getPhotographes()
    {
        return $this->photographes;
    }

    /**
     * Set createurs
     *
     * @param string $createurs
     *
     * @return TranchesEnCoursModeles
     */
    public function setCreateurs($createurs)
    {
        $this->createurs = $createurs;

        return $this;
    }

    /**
     * Get createurs
     *
     * @return string
     */
    public function getCreateurs()
    {
        return $this->createurs;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return TranchesEnCoursModeles
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set pretepourpublication
     *
     * @param boolean $pretepourpublication
     *
     * @return TranchesEnCoursModeles
     */
    public function setPretepourpublication($pretepourpublication)
    {
        $this->pretepourpublication = $pretepourpublication;

        return $this;
    }

    /**
     * Get pretepourpublication
     *
     * @return boolean
     */
    public function getPretepourpublication()
    {
        return $this->pretepourpublication;
    }
}
