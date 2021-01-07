<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class FeedbackSent extends AbstractEmail {

    private string $message;

    public function __construct(TranslatorInterface $translator, Users $user, string $message)
    {
        parent::__construct($translator, $user);
        $this->message = $message;
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
        return 'Feedback utilisateur';
    }

    public function getTextBody() : string {
        return $this->message;
    }

    public function getHtmlBody(Environment $twig) : string {
        return $this->message;
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} sent a feedback";
    }
}
