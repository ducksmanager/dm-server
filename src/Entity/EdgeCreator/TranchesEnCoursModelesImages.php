<?php

namespace App\Entity\EdgeCreator;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursModelesImages
 *
 * @ORM\Table(name="tranches_en_cours_modeles_images", indexes={@ORM\Index(name="tranches_en_cours_modeles_images___fk_image", columns={"ID_Image"}), @ORM\Index(name="tranches_en_cours_modeles_images___modele", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursModelesImages
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
     * @var bool
     *
     * @ORM\Column(name="EstPhotoPrincipale", type="boolean", nullable=false)
     */
    private $estphotoprincipale;

    /**
     * @var \ImagesTranches
     *
     * @ORM\ManyToOne(targetEntity="ImagesTranches")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Image", referencedColumnName="ID")
     * })
     */
    private $idImage;

    /**
     * @var \TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstphotoprincipale(): ?bool
    {
        return $this->estphotoprincipale;
    }

    public function setEstphotoprincipale(bool $estphotoprincipale): self
    {
        $this->estphotoprincipale = $estphotoprincipale;

        return $this;
    }

    public function getIdImage(): ?ImagesTranches
    {
        return $this->idImage;
    }

    public function setIdImage(?ImagesTranches $idImage): self
    {
        $this->idImage = $idImage;

        return $this;
    }

    public function getIdModele(): ?TranchesEnCoursModeles
    {
        return $this->idModele;
    }

    public function setIdModele(?TranchesEnCoursModeles $idModele): self
    {
        $this->idModele = $idModele;

        return $this;
    }


}
