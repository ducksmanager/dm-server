<?php
namespace App\Helper;

use App\Entity\Dm\Numeros;

class PublicationHelper {
    public static function getPublicationCode(Numeros $issue): string {
        return implode('/', [$issue->getPays(), $issue->getMagazine()]);
    }
}
