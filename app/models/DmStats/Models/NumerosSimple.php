<?php

namespace DmStats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * NumerosSimple
 *
 * @ORM\Table(name="numeros_simple", indexes={@ORM\Index(name="ID_Utilisateur", columns={"ID_Utilisateur"}), @ORM\Index(name="numeros_simple_issue", columns={"Publicationcode", "Numero"})})
 * @ORM\Entity
 */
class NumerosSimple extends \DmStats\Models\BaseModel
{
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
     * @var \DmStats\Models\AuteursPseudosSimple
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="DmStats\Models\AuteursPseudosSimple")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Utilisateur", referencedColumnName="ID_User")
     * })
     */
    private $idUtilisateur;



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

    /**
     * Set idUtilisateur
     *
     * @param \DmStats\Models\AuteursPseudosSimple $idUtilisateur
     *
     * @return NumerosSimple
     */
    public function setIdUtilisateur(\DmStats\Models\AuteursPseudosSimple $idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get idUtilisateur
     *
     * @return \DmStats\Models\AuteursPseudosSimple
     */
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }
}
