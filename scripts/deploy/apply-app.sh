#!/usr/bin/env bash

webdir=/var/www/html
webdir_new=${webdir}_new

cd ${webdir_new}

chmod -R +x scripts && \
touch pimple.json && \
chown www-data:www-data pimple.json && \
\
composer update --no-dev --prefer-dist -o && \
echo "Generating swagger.json..." && \
php scripts/generate-swagger.php && \
echo "Done." && \
\
bash scripts/update-schemas.sh 0 && \
echo -e "\nThe schema update has to be applied now. Afterwards press y to continue the deployment process. Continue ? (y/n)" && read answer && if echo "$answer" | grep -iq "^y" ;then
  rm -rf ${webdir} && mv ${webdir_new} ${webdir} && \
  echo "Deployed:" && cat ${webdir}/deployment_commit_id.txt
fi
