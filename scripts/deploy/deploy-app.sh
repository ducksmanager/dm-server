#!/usr/bin/env bash

deploy() {
  docker exec ${container_name} /bin/bash ${webdir}/scripts/deploy/backup-app.sh && \
  \
  for f in app assets scripts test index.php composer.json; \
  do \
    docker exec ${container_name} rm -rf ${webdir}/${f} && docker cp ${f} ${container_name}:${webdir}; \
  done \
  \
  && docker exec ${container_name} /bin/bash ${webdir}/scripts/deploy/apply-app.sh `git rev-parse HEAD`
}

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

echo "Generating schema update..."
docker exec ${container_name} /bin/bash ${webdir}/scripts/update-schemas.sh 0

echo -e "\nThe schema update will be applied at the end of the deployment process. Continue ? (y/n)"
read answer
if echo "$answer" | grep -iq "^y" ;then
  deploy
  docker exec ${container_name} /bin/bash -c "bash ${webdir}/scripts/update-schemas.sh 1"
fi