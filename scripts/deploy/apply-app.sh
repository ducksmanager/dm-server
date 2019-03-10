#!/usr/bin/env bash

webdir=/var/www/html
webdir_new=${webdir}_new

cd ${webdir_new}

chmod -R +x scripts && \
APP_ENV=prod composer install --no-dev --optimize-autoloader && \
\
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clear
\
bash scripts/update-schemas.sh 0 && \
echo -e "\nThe schema update has to be applied now. Afterwards press y to continue the deployment process. Continue ? (y/n)" && read answer && if echo "$answer" | grep -iq "^y" ;then
  rm -rf ${webdir} && mv ${webdir_new} ${webdir} && \
  echo "Deployed:" && cat ${webdir}/deployment_commit_id.txt
fi
