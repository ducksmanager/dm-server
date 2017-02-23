#!/usr/bin/env bash

webdir=/var/www/html/dm-server

cd ${webdir}

composer dumpautoload && \
echo '`git rev-parse HEAD`' > deployment_commit_id.txt && \
echo "Deployed:" && cat deployment_commit_id.txt