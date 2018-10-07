<?php
namespace DmServer\Emails;

use DmServer\DmServer;

class UserSuggestedBookstoreEmail extends EmailHelper {

    function getFrom()
    {
        return [$this->user->getUsername(). '@' .DmServer::$settings['smtp_origin_email_domain_ducksmanager']];
    }

    function getTo()
    {
        return [DmServer::$settings['smtp_username']];
    }

    function getSubject()
    {
        return 'Ajout de bouquinerie';
    }

    function getTextBody()
    {
        return 'Validation : https://www.ducksmanager.net/backend/bouquineries.php';
    }

    function getHtmlBody()
    {
        return '<a href="https://www.ducksmanager.net/backend/bouquineries.php">Validation</a>';
    }

    public function __toString()
    {
        return "user {$this->user->getUsername()} suggested a bookcase";
    }


}
