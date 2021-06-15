<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PresentationSentenceRefused extends AbstractEmail {

    public function getFrom() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    public function getFromName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    public function getTo() : string {
        return $this->user->getEmail();
    }

    public function getToName() : string {
        return $this->user->getUsername();
    }

    public function getSubject() : string {
        return $this->translator->trans('EMAIL_PRESENTATION_SENTENCE_REFUSED_SUBJECT');
    }

    public function getTextBody() : string {
        return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return $twig->render('emails/presentation-sentence-refused.html.twig', ['user' => $this->user] + $_ENV);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} got their presentation sentence refused";
    }
}
