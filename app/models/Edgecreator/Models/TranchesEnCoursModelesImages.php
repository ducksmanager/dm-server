<?php

namespace Edgecreator\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesEnCoursModelesImages
 *
 * @ORM\Table(name="tranches_en_cours_modeles_images", indexes={@ORM\Index(name="tranches_en_cours_modeles_images___fk_image", columns={"ID_Image"}), @ORM\Index(name="tranches_en_cours_modeles_images___modele", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursModelesImages extends \Edgecreator\Models\BaseModel
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
     * @var \Edgecreator\Models\ImagesTranches
     *
     * @ORM\ManyToOne(targetEntity="Edgecreator\Models\ImagesTranches")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Image", referencedColumnName="ID")
     * })
     */
    private $idImage;

    /**
     * @var \Edgecreator\Models\TranchesEnCoursModeles
     *
     * @ORM\ManyToOne(targetEntity="Edgecreator\Models\TranchesEnCoursModeles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Modele", referencedColumnName="ID")
     * })
     */
    private $idModele;



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
     * Set estphotoprincipale.
     *
     * @param bool $estphotoprincipale
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setEstphotoprincipale($estphotoprincipale)
    {
        $this->estphotoprincipale = $estphotoprincipale;

        return $this;
    }

    /**
     * Get estphotoprincipale.
     *
     * @return bool
     */
    public function getEstphotoprincipale()
    {
        return $this->estphotoprincipale;
    }

    /**
     * Set idImage.
     *
     * @param \Edgecreator\Models\ImagesTranches|null $idImage
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setIdImage(\Edgecreator\Models\ImagesTranches $idImage = null)
    {
        $this->idImage = $idImage;

        return $this;
    }

    /**
     * Get idImage.
     *
     * @return \Edgecreator\Models\ImagesTranches|null
     */
    public function getIdImage()
    {
        return $this->idImage;
    }

    /**
     * Set idModele.
     *
     * @param \Edgecreator\Models\TranchesEnCoursModeles|null $idModele
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setIdModele(\Edgecreator\Models\TranchesEnCoursModeles $idModele = null)
    {
        $this->idModele = $idModele;

        return $this;
    }

    /**
     * Get idModele.
     *
     * @return \Edgecreator\Models\TranchesEnCoursModeles|null
     */
    public function getIdModele()
    {
        return $this->idModele;
    }
}
