#!/usr/bin/env bash

webdir=/var/www/html/dm-server

mkdir -p ${webdir}_old && rm -rf ${webdir}_old/*

for f in .htaccess app assets scripts test favicon.ico index.php composer.json docker-compose.yml deployment_commit_id.txt
do
  if [ -d "${webdir}/$f" ] || [ -f "${webdir}/$f" ]; then
    cp -rp "${webdir}/$f" ${webdir}_old
  else
    echo "Warning: ${webdir}/$f does not exist"
  fi
done
