<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ResetPasswordEmail extends AbstractEmail {

    private string $token;

    public function __construct(TranslatorInterface $translator, Users $user, string $token)
    {
        parent::__construct($translator, $user);
        $this->token = $token;
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
        return $this->translator->trans('EMAIL_RESET_PASSWORD_SUBJECT');
    }

    public function getTextBody() : string {
        return '';
    }

    public function getHtmlBody(Environment $twig) : string {
        return $twig->render('emails/reset-password.html.twig', [
            'user' => $this->user,
            'token' => $this->token
        ] + $_ENV);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
