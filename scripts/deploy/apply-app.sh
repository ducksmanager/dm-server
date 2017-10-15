#!/usr/bin/env bash

webdir=/var/www/html/dm-server
webdir_new=${webdir}_new

cd ${webdir_new}

rm -rf ${webdir} && mv ${webdir_new} ${webdir}
echo "Deployed:" && cat ${webdir}/deployment_commit_id.txt