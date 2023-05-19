PHP = /usr/local/opt/php@8.2/bin/php
COMPOSER = /usr/local/opt/composer/bin/composer

fresh:
	$(PHP) artisan migrate:fresh

phpstan:
	$(PHP) vendor/bin/phpstan analyse

up:
	$(COMPOSER) up

install:
	$(COMPOSER) install

test:
	$(PHP) vendor/bin/phpunit $(arg)

fetch-clips:
	$(PHP) artisan app:fetch-clips-command

fetch-games:
	$(PHP) artisan app:fetch-games-command

update-clips:
	$(PHP) artisan app:update-clips-command

octane-start:
	$(PHP) artisan octane:start --watch
