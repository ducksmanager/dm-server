#!/bin/bash

cd /var/www/html/dm-server/app
grep -Po '^models_namespace=\K.+' config/schemas.ini | \
  while read -r namespace; do
    ../vendor/bin/doctrine orm:schema-tool:create --namespace=${namespace}
  done
