<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numeros
 *
 * @ORM\Table(name="numeros", uniqueConstraints={@ORM\UniqueConstraint(name="Pays", columns={"Pays", "Magazine", "Numero", "ID_Utilisateur"})}, indexes={@ORM\Index(name="Pays_Magazine_Numero", columns={"Pays", "Magazine", "Numero"}), @ORM\Index(name="Pays_Magazine_Numero_DateAjout", columns={"DateAjout", "Pays", "Magazine", "Numero"}), @ORM\Index(name="Utilisateur", columns={"ID_Utilisateur"})})
 * @ORM\Entity
 */
class Numeros extends \Dm\Models\BaseModel
{
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
     * @ORM\Column(name="Numero", type="string", length=8, nullable=false)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="Etat", type="string", length=0, nullable=false)
     */
    private $etat;

    /**
     * @var int
     *
     * @ORM\Column(name="ID_Acquisition", type="integer", nullable=false, options={"default"="-1"})
     */
    private $idAcquisition = '-1';

    /**
     * @var bool
     *
     * @ORM\Column(name="AV", type="boolean", nullable=false)
     */
    private $av;

    /**
     * @var int
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     */
    private $idUtilisateur;

    /**
     * @var int
     *
     * @ORM\Column(name="DateAjout", type="integer", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateajout = 'CURRENT_TIMESTAMP';

    /**
     * @var int
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set pays.
     *
     * @param string $pays
     *
     * @return Numeros
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
     * @return Numeros
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
     * @return Numeros
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
     * Set etat.
     *
     * @param string $etat
     *
     * @return Numeros
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat.
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set idAcquisition.
     *
     * @param int $idAcquisition
     *
     * @return Numeros
     */
    public function setIdAcquisition($idAcquisition)
    {
        $this->idAcquisition = $idAcquisition;

        return $this;
    }

    /**
     * Get idAcquisition.
     *
     * @return int
     */
    public function getIdAcquisition()
    {
        return $this->idAcquisition;
    }

    /**
     * Set av.
     *
     * @param bool $av
     *
     * @return Numeros
     */
    public function setAv($av)
    {
        $this->av = $av;

        return $this;
    }

    /**
     * Get av.
     *
     * @return bool
     */
    public function getAv()
    {
        return $this->av;
    }

    /**
     * Set idUtilisateur.
     *
     * @param int $idUtilisateur
     *
     * @return Numeros
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur.
     *
     * @return int
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set dateajout.
     *
     * @param int $dateajout
     *
     * @return Numeros
     */
    public function setDateajout($dateajout)
    {
        $this->dateajout = $dateajout;

        return $this;
    }

    /**
     * Get dateajout.
     *
     * @return int
     */
    public function getDateajout()
    {
        return $this->dateajout;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
