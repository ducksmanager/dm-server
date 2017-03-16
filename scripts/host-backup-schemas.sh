#!/bin/bash

container_name=$1
backup_dir=$2
config_file_path=dm-server/app/config/config.db.ini

if [ -z "$container_name" ]; then
	echo "No container name provided. Usage : $0 <container_name> <backup_dir>"
	exit 1
fi

if [ -z "$backup_dir" ]; then
	echo "No backup directory provided. Usage : $0 <container_name> <backup_dir>"
	exit 1
fi

docker exec ${container_name} grep -Po '^(host|dbname|username|password)=\K.+' ${config_file_path} | sed '$!N;$!N;$!N;s/\n/\t/g' | \
  while read -r host dbname username password; do
    backup_file=${backup_dir}/backup_${container_name}_${dbname}.sql
    echo "Backing up $dbname from $host to $backup_file"
    docker exec ${container_name} mysqldump -u${username} -p${password} -h ${host} ${dbname} > ${backup_file}
    echo "Compressing $backup_file"
    time 7z a -m0=lzma2 ${backup_file}.7z ${backup_file}
    if [ $? -eq 0 ]; then
      rm -f ${backup_file}
      echo "Done"
    else
      echo "Backup compression failed."
    fi
  done