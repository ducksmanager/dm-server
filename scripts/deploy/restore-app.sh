#!/usr/bin/env bash

webdir=/var/www/html/dm-server
deployment_commit_id=$1

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

for f in app assets scripts test index.php composer.json deployment_commit_id.txt
do
  rm -rf ${webdir}/{app,assets,test,index.php,composer.json,deployment_commit_id.txt}
done

for f in app assets scripts test index.php composer.json
do
  cp -rp ${webdir}_old/{app,assets,test,index.php,composer.json,deployment_commit_id.txt} "${webdir}"
done

bash "$DIR/apply-app.sh" ${deployment_commit_id}