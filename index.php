<?php

// web/index.php
require_once __DIR__.'/vendor/autoload.php';

$app = new \Wtd\WtdApp();

$app->match('/', function () {
    return '';
});
$app->run();
