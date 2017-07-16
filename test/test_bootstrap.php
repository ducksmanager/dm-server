<?php
$cacertPath = getcwd().DIRECTORY_SEPARATOR.'cacert.pem';
if (!file_exists($cacertPath)) {
    file_put_contents($cacertPath, file_get_contents('https://curl.haxx.se/ca/cacert.pem', false, stream_context_create([
        'verifypeer' => false,
        'verifyhost' => false
    ])));
}

$loader = require __DIR__ . '/../vendor/autoload.php';

\DmServer\DmServer::$isTestContext = true;
\DmServer\Test\TestCommon::createEntityManagers();