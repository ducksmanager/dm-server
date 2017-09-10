<?php
namespace DmServer;

use Dm\Models\Numeros;

class PublicationHelper {
    public static function getPublicationCode(Numeros $issue) {
        return implode('/', [$issue->getPays(), $issue->getMagazine()]);
    }
}