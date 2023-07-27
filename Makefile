PHP = /usr/local/opt/php@8.2/bin/php
COMPOSER = /usr/local/opt/composer/bin/composer

fresh:
	$(PHP) artisan migrate:fresh

ready:
	make paratest
	make phpstan

seed:
	$(PHP) artisan app:fetch-clips-command --startedAt=2023-01
	$(PHP) artisan app:fetch-clips-command --startedAt=2023-02
	$(PHP) artisan app:fetch-clips-command --startedAt=2023-03
	$(PHP) artisan app:fetch-clips-command --startedAt=2022-01
	$(PHP) artisan app:fetch-clips-command --startedAt=2022-02
	$(PHP) artisan app:fetch-clips-command --startedAt=2022-03

phpstan:
	$(PHP) vendor/bin/phpstan analyse

up:
	$(COMPOSER) up

install:
	$(COMPOSER) install

test:
	$(PHP) vendor/bin/phpunit $(arg)

paratest:
	$(PHP) vendor/bin/paratest --processes=5

fetch-clips:
	$(PHP) artisan app:fetch-clips-command

update-clips:
	$(PHP) artisan app:update-clips-command

regularize-games:
	$(PHP) artisan app:regularize-games-created-at

algolia-flush:
	$(PHP) artisan scout:flush "App\Models\Clip"

algolia-import:
	$(PHP) artisan scout:import "App\Models\Clip"