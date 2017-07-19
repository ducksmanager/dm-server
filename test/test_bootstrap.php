<?php
$cacertPath = getcwd().DIRECTORY_SEPARATOR.'cacert.pem';
if (!file_exists($cacertPath)) {
    $streamContext = stream_context_create();
    stream_context_set_params($streamContext, [
        'verifypeer' => false,
        'verifyhost' => false
    ]);
    file_put_contents($cacertPath, file_get_contents('https://curl.haxx.se/ca/cacert.pem', false, $streamContext));
}

$loader = require __DIR__ . '/../vendor/autoload.php';

\DmServer\DmServer::$isTestContext = true;
\DmServer\Test\TestCommon::createEntityManagers();