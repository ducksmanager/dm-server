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
    backup_subdir=${backup_dir}/db_csv_${dbname}

    echo "Backing up $dbname from $host to $backup_subdir"
    rm -rf ${backup_subdir} && \
    docker exec ${container_name} /bin/bash -c "rm -rf /tmp/export && mkdir -p /tmp/export && chmod 777 /tmp/export" && \
    docker exec ${container_name} /bin/bash -c "mysqldump -uroot -pchangeme -h ${host} --tab=/tmp/export --skip-dump-date ${dbname} && for i in /tmp/export/*.txt; do mv \$i \"$(basename \$i .txt).csv\"; done" && \
    docker cp ${container_name}:/tmp/export ${backup_subdir}
  
    git -C ${backup_subdir} add ${backup_subdir}/* &&
    git -C ${backup_subdir} commit -m "Backup $today"

    echo "Compressing backup_subdir"
    rm -f ${backup_subdir}.zip && zip ${backup_subdir}.zip -r ${backup_subdir}
    if [ $? -eq 0 ]; then
      echo "Backed up locally"
      if [ -z "$remote_backup_config" ]; then
        echo "No remote backup configuration was provided, skipping remote backup"
      else
        scp ${backup_subdir}.zip ${remote_backup_config}/db_${dbname}.zip
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