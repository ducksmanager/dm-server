<?php
namespace Wtd;

use Wtd\Models\Numeros;

class PublicationHelper {
    static function getPublicationCode(Numeros $issue) {
        return implode('/', [$issue->getPays(), $issue->getMagazine()]);
    }
}