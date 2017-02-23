#!/usr/bin/env bash

webdir=/var/www/html/dm-server

cd ${webdir}

composer dumpautoload && \
git rev-parse HEAD > deployment_commit_id.txt && \
echo "Deployed:" && cat deployment_commit_id.txt
