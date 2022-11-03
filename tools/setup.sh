#!/bin/bash
set -e # fail on any error

SUDO=''
if [ "$(uname)" != 'Darwin' ]; then
    SUDO='sudo'
fi

PWD=`pwd`
cd `dirname $0`/..

# setup laravel
find storage -type d | $SUDO xargs chmod 777
find bootstrap/cache -type d | $SUDO xargs chmod 777

npm install
composer install
npm run build

cd $PWD
