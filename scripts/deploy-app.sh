#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

docker exec ${container_name} /bin/bash ${webdir}/scripts/backup-app.sh \
\
&& docker exec ${container_name} rm -rf ${webdir}/app ${webdir}/assets ${webdir}/test \
&& docker cp app ${container_name}:${webdir} \
&& docker cp assets ${container_name}:${webdir} \
&& docker cp test ${container_name}:${webdir} \
&& docker cp index.php ${container_name}:${webdir} \
&& docker cp composer.json ${container_name}:${webdir} \
\
&& docker exec ${container_name} /bin/bash ${webdir}/scripts/apply-app.sh
