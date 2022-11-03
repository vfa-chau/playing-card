start: stop
	@docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d

stop:
	@docker-compose down

stop_orphans:
	@docker-compose down --remove-orphans

logs:
	@docker-compose logs -f

status:
	@docker-compose ps

build:
	@docker-compose -f docker-compose.yml -f docker-compose.dev.yml build php

setup:
	@tools/setup_docker.sh

test: check_format
	@tools/runtest.sh

test_coverage: check_format
	@tools/runtest.sh coverage

check_format:
	@vendor/bin/php-cs-fixer fix --dry-run --verbose --diff

fix_format:
	@vendor/bin/php-cs-fixer fix --verbose
