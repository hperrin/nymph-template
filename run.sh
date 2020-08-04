#! /bin/bash

set -e

if [ ! -d "app/vendor" ]; then
  ./composer.sh install --ignore-platform-reqs
fi

if [ ! -d "app/node_modules" ]; then
  ./npm.sh install
fi

if [ ! -d "app/dist" ]; then
  ./npm.sh run build
fi

if [ ! -f "db_password.txt" ]; then
  dd if=/dev/urandom bs=64 count=1 | base64 > ./db_password.txt
  dd if=/dev/urandom bs=64 count=1 | base64 > ./db_root_password.txt
fi

if [ ! -d "db_data" ]; then
  mkdir db_data
fi

if [ ! -d "mail" ]; then
  mkdir mail
fi

if [ ! -f "tilmeld_secret.txt" ]; then
  dd if=/dev/urandom bs=32 count=1 | base64 > ./tilmeld_secret.txt
fi

docker-compose up $*
