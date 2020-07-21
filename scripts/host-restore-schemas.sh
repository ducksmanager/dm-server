#!/bin/bash

backup_dir=$1
target_database_name=$2
config_file_path=app/config/config.db.ini

getEnv() {
  docker exec ${host} printenv | grep -Po "(?<=$1=).+$"
}

usage="Usage : $0 <backup_dir> <target_database_name>"

if [ -z "$backup_dir" ]; then
	echo "No backup directory provided. Usage : $usage"
	exit 1
fi

if [ -z "$target_database_name" ]; then
	echo "No target database name provided. Usage : $usage"
	exit 1
fi

docker-compose -f docker-compose-dev.yml config --services | grep '^db' | \
  while read -r service; do
    host=${service}
    dbname=`getEnv MYSQL_DATABASE`
    if [ "${dbname}" != "${target_database_name}" ]; then
      continue
    fi

    db_password=`getEnv MYSQL_ROOT_PASSWORD`
    backup_file="$backup_dir/backup_dm-server-$dbname.sql.gz"

    zcat ${backup_file} | docker exec -i ${host} mysql -u root -p${db_password} ${dbname}
    if [ $? -eq 0 ]; then
      echo "Done"
    else
      echo "Restore failed."
    fi
  done
