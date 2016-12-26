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
     * @var string
     *
     * @ORM\Column(name="Nom", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="Texte", type="text", length=65535, nullable=true)
     */
    private $texte;



    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set texte
     *
     * @param string $texte
     *
     * @return BibliothequeContributeurs
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }
}
