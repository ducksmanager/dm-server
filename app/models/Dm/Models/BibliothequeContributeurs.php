<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * BibliothequeContributeurs
 *
 * @ORM\Table(name="bibliotheque_contributeurs")
 * @ORM\Entity
 */
class BibliothequeContributeurs extends \Dm\Models\BaseModel
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
     * @var string|null
     *
     * @ORM\Column(name="Nom", type="string", length=30, nullable=true)
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="Texte", type="text", length=65535, nullable=true)
     */
    private $texte;



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
     * Set nom.
     *
     * @param string|null $nom
     *
     * @return BibliothequeContributeurs
     */
    public function setNom($nom = null)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string|null
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set texte.
     *
     * @param string|null $texte
     *
     * @return BibliothequeContributeurs
     */
    public function setTexte($texte = null)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte.
     *
     * @return string|null
     */
    public function getTexte()
    {
        return $this->texte;
    }
}
