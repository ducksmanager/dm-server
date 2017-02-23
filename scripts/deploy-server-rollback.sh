#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

docker exec ${container_name} rm -rf ${webdir}/{app,assets,test,index.php,composer.json,deployment_commit_id.txt} \
&& docker exec ${container_name} /bin/bash -c "cp -rp ${webdir}_old/{app,assets,test} ${webdir} && cp ${webdir}_old/{index.php,composer.json,deployment_commit_id.txt} ${webdir}" \
\
&& docker exec ${container_name} /bin/bash -c "cd ${webdir} && composer dumpautoload && echo '`git rev-parse HEAD`' > deployment_commit_id.txt && echo \"Reverted to:\" && cat deployment_commit_id.txt"