#!/bin/bash

backup_dir=$1
remote_backup_config=$2 # For instance user@192.168.0.2:/home/user/workspace/mybackup
today=$(date +%Y-%m-%d)

set -e

getEnv() {
  docker exec "$1" printenv | grep -Po "(?<=$2=).+$"
}

if [ -z "$backup_dir" ]; then
  echo "No backup directory provided. Usage : $0 <backup_dir> [<remote_backup_config>]"
  exit 1
fi

db_password=$(getEnv db MYSQL_ROOT_PASSWORD)
backup_file="$backup_dir/backup_dm-server-db.sql.gz"

echo "Backing up databases from db to $backup_dir"
docker exec db mysqldump -uroot -p"${db_password}" --all-databases | gzip -c > "${backup_file}"
echo "Backed up locally"

if [ -z "$remote_backup_config" ]; then
  echo "No remote backup configuration was provided, skipping remote backup"
else
  backup_file_remote="$remote_backup_config/backup_dm-server-db-$today.sql.gz"
  scp "${backup_file}" "${backup_file_remote}"
  if [ $? -eq 0 ]; then
    echo "Backed up remotely"
  else
    echo "Remote backup failed."
  fi
fi

