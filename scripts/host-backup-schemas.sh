#!/bin/bash

container_name=$1
backup_dir=$2
remote_backup_config=$3 # For instance user@192.168.0.2:/home/user/workspace/mybackup
config_file_path=dm-server/app/config/config.db.ini
today=`date +%Y-%m-%d`

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
    backup_file=backup_${container_name}_${dbname}.sql
    backup_path=${backup_dir}/${backup_file}

    echo "Backing up $dbname from $host to $backup_path"
    docker exec ${container_name} mysqldump -u${username} -p${password} -h ${host} ${dbname} > ${backup_path}
    echo "Compressing $backup_path"
    time 7z a -m0=lzma2 ${backup_path}.7z ${backup_path}
    if [ $? -eq 0 ]; then
      rm -f ${backup_path}
      echo "Backed up locally"
      if [ -z "$remote_backup_config" ]; then
        echo "No remote backup configuration was provided, skipping remote backup"
      else
        scp ${backup_path}.7z ${remote_backup_config}/${today}-${backup_file}.7z
        if [ $? -eq 0 ]; then
          echo "Backed up remotely"
        else
          echo "Remote backup failed."
        fi
      fi
    else
      echo "Backup compression failed."
    fi
  done