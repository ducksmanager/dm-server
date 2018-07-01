#!/usr/bin/env bash

webdir=/var/www/html
deployment_commit_id=$1

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

for f in .env .htaccess app assets scripts test favicon.ico index.php composer.json docker-compose.yml deployment_commit_id.txt
do
  rm -rf "${webdir}/$f"
done

for f in .env .htaccess app assets scripts test favicon.ico index.php composer.json docker-compose.yml deployment_commit_id.txt
do
  if [ -d "${webdir}_old/$f" ] || [ -f "${webdir}_old/$f" ]; then
    cp -rp "${webdir}_old/$f" "${webdir}"
  else
    echo "Warning: ${webdir}_old/$f does not exist"
  fi
done

bash "$DIR/apply-app.sh" ${deployment_commit_id}
