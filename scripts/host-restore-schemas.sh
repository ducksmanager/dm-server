#!/bin/bash

source_container_name=$1
target_container_name=$2
backup_dir=$3
config_file_path=dm-server/app/config/config.db.ini

usage="Usage : $0 <source_container_name> <target_container_name> <backup_dir>"

if [ -z "$source_container_name" ]; then
	echo "No container name provided. Usage : $usage"
	exit 1
fi

if [ -z "$target_container_name" ]; then
	echo "No container name provided. Usage : $usage"
	exit 1
fi

if [ -z "$backup_dir" ]; then
	echo "No backup directory provided. Usage : $usage"
	exit 1
fi

docker exec ${target_container_name} grep -Po '^(host|dbname|username|password)=\K.+' ${config_file_path} | sed '$!N;$!N;$!N;s/\n/\t/g' | \
  while read -r host dbname username password; do
    backup_filename=backup_${source_container_name}_${dbname}.sql
    backup_file=${backup_dir}/${backup_filename}
    echo "Extracting $dbname from $backup_file.7z"
    7z e -y ${backup_file}.7z -o${backup_dir}
    echo "Copying $backup_file to $host"
    docker cp ${backup_file} ${target_container_name}:/tmp
    echo "Restoring $dbname from $backup_filename to $host"
    docker exec ${target_container_name} /bin/bash -c "mysql -u${username} -p${password} -h ${host} ${dbname} < /tmp/$backup_filename"
    if [ $? -eq 0 ]; then
      rm -f ${backup_file}
      echo "Done"
    else
      echo "Restore failed."
    fi
  done