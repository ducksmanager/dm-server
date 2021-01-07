<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class BookstoreApproved extends AbstractEmail {

    private string $locale;
    private ?int $newMedalLevel;

    public function __construct(TranslatorInterface $translator, string $locale, Users $user, ?int $newMedalLevel = null) {
        parent::__construct($translator, $user);
        $this->locale = $locale;
        $this->newMedalLevel = $newMedalLevel;
    }

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
        return $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_SUBJECT');
    }

    public function getTextBody() : string {
        return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return $twig->render('emails/bookstore-approved.html.twig', [
            'user' => $this->user,
            'newMedalLevel' => $this->newMedalLevel,
            'locale' => $this->locale,
        ] + $_ENV);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }
}
