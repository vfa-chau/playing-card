#!/bin/sh

while getopts dmc: OPT
do
    case $OPT in
        d)  NODAEMON=1
            ;;
        m)  NOMIGRATION=1
            ;;
        c)  COMMAND=$OPTARG
            ;;
    esac
done
shift $((OPTIND - 1))

host=$DBSERVER
port=3306

echo -n "waiting for TCP connection to $host:$port..."

loop=0
while ! nc -vz $host $port > /dev/null 2>&1
do
    echo -n .
    sleep 1
    loop=$(( loop + 1 ))
    if [ $loop -eq 20 ]; then
        echo -n "connection timeout to $host:$port"
        exit 1
    fi
done
echo "connected"

if [ $TARGET ]; then
    cp .env.$TARGET .env
fi

if [ ! $NOMIGRATION ]; then
    su www-data -s /bin/bash -c -- php artisan migrate
fi

if [ ! $NODAEMON ]; then
    php-fpm
fi

if [ "$COMMAND" ]; then
    $COMMAND
fi
