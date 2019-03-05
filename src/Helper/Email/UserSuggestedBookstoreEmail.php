<?php
namespace App\Helper\Email;

use App\Helper\EmailHelper;

class UserSuggestedBookstoreEmail extends EmailHelper {

    protected function getFrom() : string {
        return $this->user->getUsername(). '@' .$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER'];
    }

    protected function getFromName() : string {
        return $this->user->getUsername(). '@' .$_ENV['SMTP_ORIGIN_EMAIL_DOMAIN_DUCKSMANAGER'];
    }

    protected function getTo() : string {
        return $_ENV['SMTP_USERNAME'];
    }

    protected function getToName() : string {
        return $_ENV['SMTP_FRIENDLYNAME'];
    }

    protected function getSubject() : string {
        return 'Ajout de bouquinerie';
    }

    protected function getTextBody() : string {
        return 'Validation : https://www.ducksmanager.net/backend/bouquineries.php';
    }

    protected function getHtmlBody() : string {
        return '<a href="https://www.ducksmanager.net/backend/bouquineries.php">Validation</a>';
    }

    public function __toString() : string {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }
}
