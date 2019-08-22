<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;

class ResetPasswordEmail extends AbstractEmail {

    private $translator;
    private $token;

    public function __construct(Swift_Mailer $mailer, TranslatorInterface $translator, LoggerInterface $logger, Users $user, string $token)
    {
        parent::__construct($mailer, $user, $logger);
        $this->translator = $translator;
        $this->token = $token;
    }

    protected function getFrom() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    protected function getFromName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    public function getTo() : string {
        return $this->user->getEmail();
    }

    protected function getToName() : string {
        return $this->user->getUsername();
    }

    public function getSubject() : string {
        return $this->translator->trans('EMAIL_RESET_PASSWORD_SUBJECT');
    }

    protected function getTextBody() : string {
        return '';
    }

    protected function getHtmlBody() : string {
        return implode('<br />', [
            $this->translator->trans('EMAIL_HELLO', ['%userName%' => $this->user->getUsername()]),

            $this->translator->trans('EMAIL_RESET_PASSWORD_BODY', [
                '%email%' => $this->user->getEmail()
            ]),

            '<a href="'.$_ENV['WEBSITE_ROOT'].'/?action=reset_password&token='.$this->token.'">'
                .$this->translator->trans('EMAIL_RESET_PASSWORD_LINK_TEXT')
            .'</a>',

            '<br />',

            $this->translator->trans('EMAIL_SIGNATURE'),

            '<img width="400" src="'.$_ENV['WEBSITE_ROOT'].'/logo_petit.png" />'
        ]);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
