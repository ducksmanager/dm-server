<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * TranchesPretesContributeurs
 *
 * @ORM\Table(name="tranches_pretes_contributeurs", indexes={@ORM\Index(name="tranches_pretes_contributeurs_contributeur_index", columns={"contributeur"}), @ORM\Index(name="tranches_pretes_contributeurs_publicationcode_issuenumber_index", columns={"publicationcode", "issuenumber"})})
 * @ORM\Entity
 */
class TranchesPretesContributeurs extends \Dm\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=15, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber;

    /**
     * @var int
     *
     * @ORM\Column(name="contributeur", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contributeur;

    /**
     * @var string
     *
     * @ORM\Column(name="contribution", type="string", length=0, nullable=false, options={"default"="createur"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $contribution = 'createur';



    /**
     * Set publicationcode.
     *
     * @param string $publicationcode
     *
     * @return TranchesPretesContributeurs
     */
    public function setPublicationcode($publicationcode)
    {
        $this->publicationcode = $publicationcode;

        return $this;
    }

    /**
     * Get publicationcode.
     *
     * @return string
     */
    public function getPublicationcode()
    {
        return $this->publicationcode;
    }

    /**
     * Set issuenumber.
     *
     * @param string $issuenumber
     *
     * @return TranchesPretesContributeurs
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber.
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set contributeur.
     *
     * @param int $contributeur
     *
     * @return TranchesPretesContributeurs
     */
    public function setContributeur($contributeur)
    {
        $this->contributeur = $contributeur;

        return $this;
    }

    /**
     * Get contributeur.
     *
     * @return int
     */
    public function getContributeur()
    {
        return $this->contributeur;
    }

    /**
     * Set contribution.
     *
     * @param string $contribution
     *
     * @return TranchesPretesContributeurs
     */
    public function setContribution($contribution)
    {
        $this->contribution = $contribution;

        return $this;
    }

    /**
     * Get contribution.
     *
     * @return string
     */
    public function getContribution()
    {
        return $this->contribution;
    }
}
