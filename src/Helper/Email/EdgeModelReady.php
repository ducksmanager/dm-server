<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class EdgeModelReady extends AbstractEmail {

    private string $publicationcode;
    private string $issuenumber;

    public function __construct(TranslatorInterface $translator, Users $user, string $publicationcode, string $issuenumber)
    {
        parent::__construct($translator, $user);
        $this->publicationcode = $publicationcode;
        $this->issuenumber = $issuenumber;
    }

    public function getFrom() : string {
        return $this->user->getUsername(). '@' .$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER'];
    }

    public function getFromName() : string {
        return $this->user->getUsername(). '@' .$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER'];
    }

    public function getTo() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    public function getToName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    public function getSubject() : string {
        return "Edge model sent : {$this->getIssueCode()}";
    }

    public function getTextBody() : string {
        return $this->getEcLink();
    }

    public function getHtmlBody(Environment $twig) : string {
        return "<a href=\"{$this->getEcLink()}\">{$this->getIssueCode()}</a>";
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} submitted the model of edge {$this->getIssueCode()}";
    }

    public function getEcLink() : string {
        return "{$_ENV['EDGECREATOR_ROOT']}/edit/{$this->getIssueCode()}";
    }

    private function getIssueCode() : string {
        return "{$this->publicationcode} {$this->issuenumber}";
    }

}
