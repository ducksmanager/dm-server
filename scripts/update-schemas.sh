#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

force=$1
if [ ! -z "${force}" ] && [ ${force} -eq 1 ]; then
  force=1
else
  force=0
fi

cd ${DIR}/..
./bin/console debug:config doctrine orm.entity_managers | grep -Po '^(?!default)[_a-z]+' | \
  while read -r em; do
    if [ ${force} -eq 1 ]; then
      echo -e "\n\nApplying SQL diff for entity manager ${em}\n"
      ./bin/console doctrine:schema:update --em=${em} --force
    else
      echo -e "\n\nDumping SQL diff for entity manager ${em}\n"
      ./bin/console doctrine:schema:update --em=${em} --dump-sql
    fi
    if [ $? -ne 0 ]; then
      echo "Schema diff failed."
      exit 1
    fi
  done
