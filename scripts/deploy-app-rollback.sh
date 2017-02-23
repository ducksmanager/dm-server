#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

docker exec ${container_name} /bin/bash /var/www/html/dm-server/scripts/restore-app.sh