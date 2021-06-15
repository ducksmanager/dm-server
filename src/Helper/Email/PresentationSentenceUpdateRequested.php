<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PresentationSentenceUpdateRequested extends AbstractEmail {

    private string $sentence;

    public function __construct(TranslatorInterface $translator, Users $user, string $sentence)
    {
        parent::__construct($translator, $user);
        $this->sentence = $sentence;
    }

    public function getFrom() : string {
        return "{$this->user->getUsername()}@{$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER']}";
    }

    public function getFromName() : string {
        return "{$this->user->getUsername()}@{$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER']}";
    }

    public function getTo() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    public function getToName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    public function getSubject() : string {
        return 'Presentation sentence update request';
    }

    public function getTextBody() : string {
         return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return implode(' ', array_map(fn(string $choice) => sprintf(
            "<a href=\"{$_ENV['WEBSITE_ROOT']}/admin/presentationSentence/$choice/?userId=%s&sentence=%s\">".ucfirst($choice)."</a>",
            $this->user->getId(),
            $this->sentence
        ), ['approve', 'refuse']));
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} wants to update the presentation sentence";
    }
}
