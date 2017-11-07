<?php

namespace Stats\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoiresPublications
 *
 * @ORM\Table(name="histoires_publications", indexes={@ORM\Index(name="index_issue", columns={"publicationcode", "issuenumber"}), @ORM\Index(name="index_story", columns={"storycode"})})
 * @ORM\Entity
 */
class HistoiresPublications extends \Stats\Models\BaseModel
{
    /**
     * @var string
     *
     * @ORM\Column(name="publicationcode", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $publicationcode;

    /**
     * @var string
     *
     * @ORM\Column(name="issuenumber", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $issuenumber;

    /**
     * @var string
     *
     * @ORM\Column(name="storycode", type="string", length=19, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $storycode;



    /**
     * Set publicationcode
     *
     * @param string $publicationcode
     *
     * @return HistoiresPublications
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
     * Set issuenumber
     *
     * @param string $issuenumber
     *
     * @return HistoiresPublications
     */
    public function setIssuenumber($issuenumber)
    {
        $this->issuenumber = $issuenumber;

        return $this;
    }

    /**
     * Get issuenumber
     *
     * @return string
     */
    public function getIssuenumber()
    {
        return $this->issuenumber;
    }

    /**
     * Set storycode
     *
     * @param string $storycode
     *
     * @return HistoiresPublications
     */
    public function setStorycode($storycode)
    {
        $this->storycode = $storycode;

        return $this;
    }

    /**
     * Get storycode
     *
     * @return string
     */
    public function getStorycode()
    {
        return $this->storycode;
    }
}
