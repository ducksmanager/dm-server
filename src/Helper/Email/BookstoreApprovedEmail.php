<?php
namespace App\Helper\Email;

use App\Entity\Dm\Users;
use App\Helper\EmailHelper;
use Swift_Mailer;
use Symfony\Contracts\Translation\TranslatorInterface;

class BookstoreApprovedEmail extends EmailHelper {

    /** @var TranslatorInterface $translator */
    private $translator;

    public function __construct(Swift_Mailer $mailer, TranslatorInterface $translator, Users $user) {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
    }

    protected function getFrom() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    protected function getFromName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    protected function getTo() : string {
        return $this->user->getEmail();
    }

    protected function getToName() : string {
        return $this->user->getUsername();
    }

    protected function getSubject() : string {
        return $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_SUBJECT');
    }

    protected function getTextBody() : string {
        return '';
    }

    protected function getHtmlBody() : string {
        return implode('<br />', [
            $this->translator->trans('EMAIL_HELLO', ['%userName%' => $this->user->getUsername()]),

            $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_INTRO'),

            // TODO Handle new medals levels

            $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_THANKS'),

            '<br />',

            $this->translator->trans('EMAIL_SIGNATURE'),

            '<img width="400" src="'.$_ENV['WEBSITE_ROOT'].'/logo_petit.png" />'
        ]);
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }
}
