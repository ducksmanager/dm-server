<?php

namespace App\Entity\Dm;

use Doctrine\ORM\Mapping as ORM;

/**
 * BouquineriesCommentaires
 *
 * @ORM\Table(name="bouquineries_commentaires", indexes={@ORM\Index(name="bouquineries_commentaires_bouquineries_ID_fk", columns={"ID_Bouquinerie"}), @ORM\Index(name="bouquineries_commentaires_users_ID_fk", columns={"ID_Utilisateur"})})
 * @ORM\Entity
 */
class BouquineriesCommentaires
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
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="text", length=65535, nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateAjout", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $creationDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="Actif", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var \Bouquineries
     *
     * @ORM\ManyToOne(targetEntity="Bouquineries")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Bouquinerie", referencedColumnName="ID")
     * })
     */
    private $bookstore;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_Utilisateur", referencedColumnName="ID")
     * })
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getBookstore(): ?Bouquineries
    {
        return $this->bookstore;
    }

    public function setBookstore(?Bouquineries $bookstore): self
    {
        $this->bookstore = $bookstore;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }


}
