#!/bin/bash

source_container_name=$1
backup_dir=$2
config_file_path=app/config/config.db.ini

getEnv() {
  while read envVariableName; do
    docker inspect --format '{{ .Config.Env }}' ${host} |  tr ' ' '\n' | grep ${envVariableName} | sed 's/^.*=//'
  done
}

usage="Usage : $0 <source_container_name> <target_container_name> <backup_dir>"

if [ -z "$source_container_name" ]; then
	echo "No container name provided. Usage : $usage"
	exit 1
fi

if [ -z "$backup_dir" ]; then
	echo "No backup directory provided. Usage : $usage"
	exit 1
fi

docker-compose config --services | grep '^db' | \
  while read -r service; do
    host=`docker-compose ps -q ${service}`
    dbname=`echo MYSQL_DATABASE | getEnv`
    db_password=`echo MYSQL_ROOT_PASSWORD | getEnv`
    backup_filename=backup_${source_container_name}_${dbname}.sql
    backup_file=${backup_dir}/${backup_filename}
    echo "Extracting $dbname from $backup_file.7z"
    7z e -y ${backup_file}.7z -o${backup_dir}
    echo "Copying $backup_file to $host"
    docker cp ${backup_file} ${host}:/tmp
    echo "Restoring $dbname from $backup_filename to $host"
    docker exec ${host} /bin/bash -c "mysql -uroot -p${db_password} -h ${host} ${dbname} < /tmp/$backup_filename"
    if [ $? -eq 0 ]; then
      rm -f ${backup_file}
      echo "Done"
    else
      echo "Restore failed."
    fi
  done
