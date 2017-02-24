#!/usr/bin/env bash

webdir=/var/www/html/dm-server

mkdir -p ${webdir}_old && rm -rf ${webdir}_old/*

for f in app assets scripts test index.php composer.json deployment_commit_id.txt
do
  cp -rp "${webdir}/$f" ${webdir}_old
done