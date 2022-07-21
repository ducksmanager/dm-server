#!/bin/bash

backup_dir=$1

getEnv() {
  docker exec db printenv | grep -Po "(?<=$1=).+$"
}

usage="Usage : $0 <backup_dir>"

if [ -z "$backup_dir" ]; then
	echo "No backup directory provided. Usage : $usage"
	exit 1
fi

db_password=$(getEnv MYSQL_ROOT_PASSWORD)
backup_file="$backup_dir/backup_dm-server-db.sql.gz"

zcat "${backup_file}" | docker exec -i db mysql -u root -p"${db_password}"
if [ $? -eq 0 ]; then
  echo "Done"
else
  echo "Restore failed."
fi
