#!/usr/bin/env bash

webdir=/var/www/html/dm-server
deployment_commit_id=$1

cd ${webdir}

composer dumpautoload -o && \
echo ${deployment_commit_id} > deployment_commit_id.txt && \
echo "Deployed:" && cat deployment_commit_id.txt