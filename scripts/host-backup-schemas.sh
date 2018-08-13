#!/bin/bash

backup_dir=$1
remote_backup_config=$2 # For instance user@192.168.0.2:/home/user/workspace/mybackup
config_file_path=app/config/config.db.ini
today=`date +%Y-%m-%d`

getEnv() {
  while read envVariableName; do
    docker inspect --format '{{ .Config.Env }}' ${host} |  tr ' ' '\n' | grep ${envVariableName} | sed 's/^.*=//'
  done
}

if [ -z "$backup_dir" ]; then
  echo "No backup directory provided. Usage : $0 <container_name> <backup_dir>"
  exit 1
fi

docker-compose config --services | grep '^db' | \
  while read -r service; do
    host=`docker-compose ps -q ${service}`
    dbname=`echo MYSQL_DATABASE | getEnv`
    db_password=`echo MYSQL_ROOT_PASSWORD | getEnv`
    backup_subdir=${backup_dir}/db_csv_${dbname}
    mkdir -p ${backup_subdir}

    echo "Backing up $dbname from $host to $backup_subdir"

    rm -rf ${backup_subdir}/* && \
    docker exec ${host} /bin/bash -c "rm -rf /tmp/export && mkdir -p /tmp/export && chmod 777 /tmp/export" && \
    docker exec ${host} /bin/bash -c "mysqldump -uroot -p${db_password} --tab=/tmp/export --skip-dump-date ${dbname} && for i in /tmp/export/*.txt; do mv \$i \"$(basename \$i .txt).csv\"; done" && \
    docker cp ${host}:/tmp/export ${backup_subdir}

    rm -rf ${backup_subdir}/inducks_*nofulltext.*

    echo "Compressing backup_subdir"
    backup_file="${backup_dir}/backup_dm-server-box_${dbname}.7z"
    rm -f ${backup_file} && 7z a -t7z ${backup_file} -m0=lzma2 -mx=9 -aoa -mfb=64 -md=32m ${backup_subdir}
    if [ $? -eq 0 ]; then
      echo "Backed up locally"
      if [ -z "$remote_backup_config" ]; then
        echo "No remote backup configuration was provided, skipping remote backup"
      else
        backup_file_remote="backup_dm-server-box_${dbname}-${today}.7z"
        scp ${backup_file} ${remote_backup_config}/${backup_file_remote}
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
