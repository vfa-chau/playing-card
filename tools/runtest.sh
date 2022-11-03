#!/bin/sh
set -e

TEST_TARGET=
OPTS="-f docker-compose.yml -f docker-compose.test.yml"
COMMAND="run --rm --user=www-data"
SUDO=''
if [ "$(uname)" != 'Darwin' ]; then
    SUDO='sudo'
fi

if [ "$1" = "coverage" ]; then
    sudo rm -rf public/test_coverage
    TEST_OPT='--coverage-html public/test_coverage'
fi

if [ "$1" = "fast" ]; then
    COMMAND="exec"
else
    if docker-compose $OPTS ps -q web > /dev/null 2>&1; then
        docker-compose $OPTS up -d web
        sleep 5
    fi
fi

docker-compose $OPTS $COMMAND php vendor/bin/phpunit $TEST_OPT $TEST_TARGET || echo done
