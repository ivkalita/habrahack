THIS_FILE := $(lastword $(MAKEFILE_LIST))

cs:
	bin/php-cs-fixer fix --verbose

cs-dry-run:
	bin/php-cs-fixer fix --verbose --dry-run

c-inst:
	composer install

test:
	bin/phpunit -c app src

stage: set-permissions cache-dev cache-test migrations-dev fixtures-dev migrations-test fixtures-test install-assets
	$(MAKE) -f $(THIS_FILE) set-permissions

dev: set-permissions cache-dev install-assets
	$(MAKE) -f $(THIS_FILE) set-permissions

prod: set-permissions cache-prod dump install-assets
	$(MAKE) -f $(THIS_FILE) set-permissions

set-permissions:
	sudo chmod -R ug+rw .
	sudo chmod -R a+rw app/cache app/logs

migrations-dev:
	php app/console doctrine:migrations:migrate --env=dev

migrations-test:
	php app/console doctrine:migrations:migrate --env=test

fixtures-dev:
	php app/console h:d:f:l --env=dev

fixtures-test:
	php app/console h:d:f:l -b PromotionBundle --env=test

clear-cache-dev: set-permissions cache-dev
	$(MAKE) -f $(THIS_FILE) set-permissions

clear-cache-prod: set-permissions cache-prod
	$(MAKE) -f $(THIS_FILE) set-permissions

cache-dev:
	php app/console cache:clear --env=dev

cache-prod:
	php app/console cache:clear --env=prod --no-debug

cache-test:
	php app/console cache:clear --env=test

update-db:
	php app/console doctrine:schema:update --force --dump-sql

reload-db:
	php app/console doctrine:database:drop --force
	php app/console doctrine:database:create
	php app/console doctrine:schema:create

load-fixtures:
	php app/console doctrine:fixtures:load

dump:
	composer dump-autoload --optimize
	php app/console assetic:dump --env=prod --no-debug

install-assets: install-web-assets

install-web-assets:
	php app/console assets:install web --symlink
