#!/bin/bash
set -e # fail on any error

SUDO=''
if [ "$(uname)" != 'Darwin' ]; then
    SUDO='sudo'
fi

PWD=`pwd`
cd `dirname $0`/..

PHP_CONTAINER=`docker-compose ps | awk '/php/{ print $1; }'`
docker exec -it $PHP_CONTAINER npm install
docker exec -it $PHP_CONTAINER composer install --no-dev
docker exec -it $PHP_CONTAINER npm run build

cd $PWD
