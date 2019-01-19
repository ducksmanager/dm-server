<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use DmServer\DmServer;
use Swift_Mailer;
use Symfony\Component\Translation\Translator;

class ResetPasswordEmail extends EmailHelper {

    private $translator;
    private $token;

    public function __construct(Swift_Mailer $mailer, Translator $translator, Users $user, string $token)
    {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
        $this->token = $token;
    }

    protected function getFrom() {
        return [DmServer::$settings['smtp_username']];
    }

    protected function getFromName() {
        return DmServer::$settings['smtp_friendlyname'];
    }

    protected function getTo() {
        return [$this->user->getEmail()];
    }

    protected function getToName() {
        return $this->user->getUsername();
    }

    protected function getSubject() {
        return $this->translator->trans('EMAIL_RESET_PASSWORD_SUBJECT');
    }

    protected function getTextBody() {
    }

    protected function getHtmlBody() {
        return implode('<br />', [
            $this->translator->trans('EMAIL_HELLO', ['%userName%' => $this->user->getUsername()]),

            $this->translator->trans('EMAIL_RESET_PASSWORD_BODY', [
                '%email%' => $this->user->getEmail()
            ]),

            '<a href="'.DmServer::$settings['website_root'].'?action=reset_password&token='.$this->token.'">'
                .$this->translator->trans('EMAIL_RESET_PASSWORD_LINK_TEXT')
            .'</a>',

            '<br />',

            $this->translator->trans('EMAIL_SIGNATURE'),

            '<img width="400" src="'.DmServer::$settings['website_root'].'logo_petit.png" />'
        ]);
    }

    public function __toString() {
        return "user {$this->user->getUsername()}'s edge(s) got published";
    }
}
