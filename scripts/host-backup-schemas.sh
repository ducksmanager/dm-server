#!/bin/bash

backup_dir=$1
remote_backup_config=$2 # For instance user@192.168.0.2:/home/user/workspace/mybackup
today=`date +%Y-%m-%d`

set -e

getEnv() {
  docker exec ${host} printenv | grep -Po "(?<=$1=).+$"
}

if [ -z "$backup_dir" ]; then
  echo "No backup directory provided. Usage : $0 <container_name> <backup_dir>"
  exit 1
fi

docker-compose config --services | grep '^db' | \
  while read -r service; do
    host=${service}
    dbname=`getEnv MYSQL_DATABASE`
    db_password=`getEnv MYSQL_ROOT_PASSWORD`
    backup_file="$backup_dir/backup_dm-server-$dbname.sql.gz"

    echo "Backing up $dbname from $host to $backup_dir"
    docker exec ${host} mysqldump -uroot -p${db_password} ${dbname} | gzip -c > ${backup_file}
    echo "Backed up locally"

    if [ -z "$remote_backup_config" ]; then
      echo "No remote backup configuration was provided, skipping remote backup"
    else
      backup_file_remote="$remote_backup_config/backup_dm-server-$dbname-$today.sql.gz"
      scp ${backup_file} ${backup_file_remote}
      if [ $? -eq 0 ]; then
        echo "Backed up remotely"
      else
        echo "Remote backup failed."
      fi
    fi
done

