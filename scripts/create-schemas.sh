#!/bin/bash

app_dir=/var/www/html/dm-server/app

cd ${app_dir}/config
php generate-db-config.php
if [ $? -eq 0 ]; then
  cd ${app_dir}
  grep -Po '^models_namespace=\K.+' config/schemas.ini | \
    while read -r namespace; do
      ../vendor/bin/doctrine orm:schema-tool:create --namespace=${namespace}
      if [ $? -ne 0 ]; then
        echo "Schema creation failed."
        exit -1
      fi
    done
else
  echo "DB config generation failed."
  exit -1
fi