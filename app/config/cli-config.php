<?php
require_once __DIR__."/../../vendor/autoload.php";
require_once __DIR__."/../Wtd.php";

use Wtd\Wtd;

$em = Wtd::getEntityManager();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($em);
