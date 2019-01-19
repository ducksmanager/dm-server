<?php
namespace DmServer\Emails;

use DmServer\DmServer;

class UserSuggestedBookstoreEmail extends EmailHelper {

    protected function getFrom() {
        return [$this->user->getUsername(). '@' .DmServer::$settings['smtp_origin_email_domain_ducksmanager']];
    }

    protected function getFromName() {
        return $this->user->getUsername(). '@' .DmServer::$settings['smtp_origin_email_domain_ducksmanager'];
    }

    protected function getTo() {
        return [DmServer::$settings['smtp_username']];
    }

    protected function getToName() {
        return DmServer::$settings['smtp_friendlyname'];
    }

    protected function getSubject() {
        return 'Ajout de bouquinerie';
    }

    protected function getTextBody() {
        return 'Validation : https://www.ducksmanager.net/backend/bouquineries.php';
    }

    protected function getHtmlBody() {
        return '<a href="https://www.ducksmanager.net/backend/bouquineries.php">Validation</a>';
    }

    public function __toString() {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }
}
