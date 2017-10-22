#!/usr/bin/env bash

deploy() {
  docker cp scripts ${container_name}:${webdir} \
  && docker exec ${container_name} /bin/bash ${webdir}/scripts/deploy/backup-app.sh \
  && docker exec ${container_name} /bin/bash -c "rm -rf ${webdir}_new && mkdir -p ${webdir}_new" \
  \
  && for f in .htaccess app assets scripts test index.php composer.json docker-compose.yml; \
  do \
    docker cp ${f} ${container_name}:${webdir}_new; \
  done \
  \
  && docker exec ${container_name} chmod -R +x ${webdir}_new/scripts \
  && docker exec ${container_name} /bin/bash -c "cd ${webdir}_new && touch development.log pimple.json && chown www-data:www-data development.log pimple.json" \
  && docker exec ${container_name} /bin/bash -c "cd ${webdir}_new && cp -r ../dm-server/vendor . && composer update --no-dev -o && echo `git rev-parse HEAD` > deployment_commit_id.txt" \
  && docker exec ${container_name} /bin/bash ${webdir}_new/scripts/update-schemas.sh 0 \
  && echo -e "\nThe schema update has to be applied now. Afterwards press y to continue the deployment process. Continue ? (y/n)" \
  && read answer \
  && if echo "$answer" | grep -iq "^y" ;then
     docker exec ${container_name} /bin/bash ${webdir}_new/scripts/deploy/apply-app.sh
  fi
}

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

deploy