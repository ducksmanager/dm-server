<?php

namespace EdgeCreator\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * TranchesEnCoursModelesImages
 *
 * @ORM\Table(name="tranches_en_cours_modeles_images", indexes={@ORM\Index(name="tranches_en_cours_modeles_images___fk_image", columns={"ID_Image"}), @ORM\Index(name="tranches_en_cours_modeles_images___modele", columns={"ID_Modele"})})
 * @ORM\Entity
 */
class TranchesEnCoursModelesImages extends \EdgeCreator\Models\BaseModel
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
     * @var boolean
     *
     * @ORM\Column(name="EstPhotoPrincipale", type="boolean", nullable=false)
     */
    private $estphotoprincipale;

    /**
     * @var \EdgeCreator\Models\ImagesTranches
     *
     * @ORM\ManyToOne(targetEntity="EdgeCreator\Models\ImagesTranches", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Image", referencedColumnName="ID")
     * })
     */
    private $image;

    /**
     * @var \EdgeCreator\Models\TranchesEnCoursModeles
     *
     * @ManyToOne(targetEntity="TranchesEnCoursModeles", inversedBy="photos")
     * @JoinColumn(name="ID_Modele", referencedColumnName="ID")
     */
    private $modele;



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
     * Set estphotoprincipale
     *
     * @param boolean $estphotoprincipale
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setEstphotoprincipale($estphotoprincipale)
    {
        $this->estphotoprincipale = $estphotoprincipale;

        return $this;
    }

    /**
     * Get estphotoprincipale
     *
     * @return boolean
     */
    public function getEstphotoprincipale()
    {
        return $this->estphotoprincipale;
    }

    /**
     * Set idImage
     *
     * @param \EdgeCreator\Models\ImagesTranches $image
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setImage(\EdgeCreator\Models\ImagesTranches $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get idImage
     *
     * @return \EdgeCreator\Models\ImagesTranches
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set idModele
     *
     * @param \EdgeCreator\Models\TranchesEnCoursModeles $modele
     *
     * @return TranchesEnCoursModelesImages
     */
    public function setModele(\EdgeCreator\Models\TranchesEnCoursModeles $modele = null)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get idModele
     *
     * @return \EdgeCreator\Models\TranchesEnCoursModeles
     */
    public function getModele()
    {
        return $this->modele;
    }
}
