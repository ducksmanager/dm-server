#!/usr/bin/env bash

role=$1
path=$2
webdir=/var/www/html

role_password=`cat "${webdir}/app/config/roles.ini" | grep -E "^$role=" | cut -d":" -f2`
authorization=`echo -n "$role:$role_password" | base64`

curl -i -XPOST \
  -H "Authorization: Basic $authorization" \
  -H "Accept: application/json" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "Cache-Control: no-cache" \
  -H "x-dm-version: 1.0" \
  "localhost/$path"
