<?php

namespace DmStats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosSimple
 *
 * @ORM\Table(name="numeros_simple", indexes={@ORM\Index(name="ID_Utilisateur", columns={"ID_Utilisateur"}), @ORM\Index(name="Numero", columns={"Numero"}), @ORM\Index(name="Publicationcode", columns={"Publicationcode"})})
 * @ORM\Entity
 */
class NumerosSimple extends \DmStats\Models\BaseModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_Utilisateur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idUtilisateur;

    /**
     * @var string
     *
     * @ORM\Column(name="Publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="Numero", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $numero;



    /**
     * Set idUtilisateur
     *
     * @param integer $idUtilisateur
     *
     * @return NumerosSimple
     */
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur
     *
     * @return integer
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set publicationcode
     *
     * @param string $publicationcode
     *
     * @return NumerosSimple
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return NumerosSimple
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
}
