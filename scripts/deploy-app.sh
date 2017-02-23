#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

docker exec ${container_name} /bin/bash ${webdir}/scripts/backup-app.sh && \
\
for f in app assets scripts test index.php composer.json; \
do \
  docker exec ${container_name} rm -rf ${webdir}/${f} && docker cp ${f} ${container_name}:${webdir}; \
done \
\
&& docker exec ${container_name} /bin/bash ${webdir}/scripts/apply-app.sh `git rev-parse HEAD`
