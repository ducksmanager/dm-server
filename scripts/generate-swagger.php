<?php
$dm_server_address=getenv('DM_SERVER_ADDRESS');

exec("php vendor/radebatz/silex2swagger/bin/silex2swagger silex2swagger:build --file=swagger.json --path=app/controllers");
file_put_contents("swagger.json", str_replace("DM_SERVER_ADDRESS", $dm_server_address, file_get_contents("swagger.json")));
