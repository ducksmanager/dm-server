#!/usr/bin/env bash

container_name=$1

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name>"
	exit 1
fi

webdir=/var/www/html/dm-server

docker exec ${container_name} /bin/bash -c "\
  mkdir -p ${webdir}_old && rm -rf ${webdir}_old/{app,assets,test} \
  && cp -rp ${webdir}/{app,assets,test} ${webdir}_old \
  && cp     ${webdir}/{index.php,composer.json,deployment_commit_id.txt} ${webdir}_old" \
\
&& docker exec ${container_name} rm -rf ${webdir}/app ${webdir}/assets ${webdir}/test \
&& docker cp app ${container_name}:${webdir} \
&& docker cp assets ${container_name}:${webdir} \
&& docker cp test ${container_name}:${webdir} \
&& docker cp index.php ${container_name}:${webdir} \
&& docker cp composer.json ${container_name}:${webdir} \
\
&& docker exec ${container_name} /bin/bash -c "cd ${webdir} && composer dumpautoload && echo '`git rev-parse HEAD`' > deployment_commit_id.txt && echo \"Deployed:\" && cat deployment_commit_id.txt"