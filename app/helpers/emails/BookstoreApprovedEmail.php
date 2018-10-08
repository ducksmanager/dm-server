<?php
namespace DmServer\Emails;

use Dm\Models\Users;
use DmServer\DmServer;
use Silex\Application\TranslationTrait;
use Swift_Mailer;

class BookstoreApprovedEmail extends EmailHelper {

    private $bookstoreName;
    private $translator;
    private $newMedalLevel;

    public function __construct(Swift_Mailer $mailer, TranslationTrait $translator, Users $user, string $bookstoreName, $newMedalLevel = null)
    {
        parent::__construct($mailer, $user);
        $this->translator = $translator;
        $this->bookstoreName = $bookstoreName;
        $this->newMedalLevel = $newMedalLevel;
    }

    function getFrom()
    {
        return [DmServer::$settings['smtp_username']];
    }

    function getTo()
    {
        return [$this->user->getEmail()];
    }

    function getSubject()
    {
        return $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_SUBJECT');
    }

    function getTextBody()
    {
    }

    function getHtmlBody()
    {
        $body = $this->translator->trans('EMAIL_HELLO', ['%userName%', $this->user->getUsername()]);

        $body.= $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_INTRO');

        if (!is_null($this->newMedalLevel)) {
            $body.= $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_MEDAL', ['%medalLevel%' => $this->newMedalLevel]);
        }

        $body.= $this->translator->trans('EMAIL_BOOKSTORE_APPROVED_THANKS');

        $body.= $this->translator->trans('EMAIL_CONFIRMATION_SIGNATURE');

        return $body;
    }

    public function __toString()
    {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }


}
