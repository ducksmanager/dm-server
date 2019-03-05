<?php
namespace App\Helper;

use App\Entity\Dm\Numeros;

class PublicationHelper {
    public static function getPublicationCode(Numeros $issue) {
        return implode('/', [$issue->getPays(), $issue->getMagazine()]);
    }
}