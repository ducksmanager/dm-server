#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

docker exec ${container_name} rm -rf /var/www/html/dm-server/app /var/www/html/dm-server/test \
&& docker cp app ${container_name}:/var/www/html/dm-server \
&& docker cp test ${container_name}:/var/www/html/dm-server \
&& docker cp index.php ${container_name}:/var/www/html/dm-server \
&& docker cp composer.json ${container_name}:/var/www/html/dm-server \
\
&& docker exec ${container_name} /bin/bash -c "cd /var/www/html/dm-server && composer dumpautoload && echo '`git rev-parse HEAD`' > deployment_commit_id.txt"