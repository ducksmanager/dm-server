#!/usr/bin/env bash

webdir=/var/www/html

mkdir -p ${webdir}_old && rm -rf ${webdir}_old/*

for f in bin config scripts src .env .env.local .htaccess composer.json composer.lock docker-compose.yml deployment_commit_id.txt favicon.ico
do
  if [ -d "${webdir}/$f" ] || [ -f "${webdir}/$f" ]; then
    cp -rp "${webdir}/$f" ${webdir}_old
  else
    echo "Warning: ${webdir}/$f does not exist"
  fi
done
