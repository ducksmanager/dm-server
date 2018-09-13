<?php

namespace Dm\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailsVentes
 *
 * @ORM\Table(name="emails_ventes", uniqueConstraints={@ORM\UniqueConstraint(name="emails_ventes__username_achat_username_vente_date_uindex", columns={"username_achat", "username_vente", "date"})})
 * @ORM\Entity
 */
class EmailsVentes extends \Dm\Models\BaseModel
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
     * @ORM\Column(name="username_achat", type="string", length=50, nullable=false)
     */
    private $usernameAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="username_vente", type="string", length=50, nullable=false)
     */
    private $usernameVente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;



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
     * Set usernameAchat.
     *
     * @param string $usernameAchat
     *
     * @return EmailsVentes
     */
    public function setUsernameAchat($usernameAchat)
    {
        $this->usernameAchat = $usernameAchat;

        return $this;
    }

    /**
     * Get usernameAchat.
     *
     * @return string
     */
    public function getUsernameAchat()
    {
        return $this->usernameAchat;
    }

    /**
     * Set usernameVente.
     *
     * @param string $usernameVente
     *
     * @return EmailsVentes
     */
    public function setUsernameVente($usernameVente)
    {
        $this->usernameVente = $usernameVente;

        return $this;
    }

    /**
     * Get usernameVente.
     *
     * @return string
     */
    public function getUsernameVente()
    {
        return $this->usernameVente;
    }

    /**
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return EmailsVentes
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
