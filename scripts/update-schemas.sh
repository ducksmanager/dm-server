#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

force=$1
if [ ! -z "${force}" ] && [ ${force} -eq 1 ]; then
  force=1
else
  force=0
fi

app_dir=${DIR}/../app

cd ${app_dir}/config
php generate-config.php ../../docker-compose.yml ../../.env
if [ $? -eq 0 ]; then
  cd ${app_dir}
  grep -Po '^models_namespace=\K.+' config/schemas.ini | \
    while read -r namespace; do
      if [ ${force} -eq 1 ]; then
        echo -e "\n\nApplying SQL diff for namespace ${namespace}\n"
        ../vendor/bin/doctrine orm:schema-tool:update --namespace=${namespace} --force
      else
        echo -e "\n\nDumping SQL diff for namespace ${namespace}\n"
        ../vendor/bin/doctrine orm:schema-tool:update --namespace=${namespace} --dump-sql
      fi
      if [ $? -ne 0 ]; then
        echo "Schema diff failed."
        exit -1
      fi
    done
else
  echo "DB config generation failed."
  exit -1
fi
