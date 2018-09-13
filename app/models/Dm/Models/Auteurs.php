<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * Auteurs
 *
 * @ORM\Table(name="auteurs")
 * @ORM\Entity
 * @deprecated
 */
class Auteurs extends \Dm\Models\BaseModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="ID_auteur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAuteur;

    /**
     * @var string
     *
     * @ORM\Column(name="NomAuteur", type="string", length=20, nullable=false)
     */
    private $nomauteur;

    /**
     * @var int
     *
     * @ORM\Column(name="NbHistoires", type="integer", nullable=false)
     */
    private $nbhistoires;

    /**
     * @var int
     *
     * @ORM\Column(name="NbHistoires_old", type="integer", nullable=false)
     */
    private $nbhistoiresOld;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateMAJ", type="date", nullable=false)
     */
    private $datemaj;



    /**
     * Get idAuteur.
     *
     * @return int
     */
    public function getIdAuteur()
    {
        return $this->idAuteur;
    }

    /**
     * Set nomauteur.
     *
     * @param string $nomauteur
     *
     * @return Auteurs
     */
    public function setNomauteur($nomauteur)
    {
        $this->nomauteur = $nomauteur;

        return $this;
    }

    /**
     * Get nomauteur.
     *
     * @return string
     */
    public function getNomauteur()
    {
        return $this->nomauteur;
    }

    /**
     * Set nbhistoires.
     *
     * @param int $nbhistoires
     *
     * @return Auteurs
     */
    public function setNbhistoires($nbhistoires)
    {
        $this->nbhistoires = $nbhistoires;

        return $this;
    }

    /**
     * Get nbhistoires.
     *
     * @return int
     */
    public function getNbhistoires()
    {
        return $this->nbhistoires;
    }

    /**
     * Set nbhistoiresOld.
     *
     * @param int $nbhistoiresOld
     *
     * @return Auteurs
     */
    public function setNbhistoiresOld($nbhistoiresOld)
    {
        $this->nbhistoiresOld = $nbhistoiresOld;

        return $this;
    }

    /**
     * Get nbhistoiresOld.
     *
     * @return int
     */
    public function getNbhistoiresOld()
    {
        return $this->nbhistoiresOld;
    }

    /**
     * Set datemaj.
     *
     * @param \DateTime $datemaj
     *
     * @return Auteurs
     */
    public function setDatemaj($datemaj)
    {
        $this->datemaj = $datemaj;

        return $this;
    }

    /**
     * Get datemaj.
     *
     * @return \DateTime
     */
    public function getDatemaj()
    {
        return $this->datemaj;
    }
}
