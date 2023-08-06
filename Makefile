PHP = /usr/local/opt/php@8.2/bin/php
COMPOSER = /usr/local/opt/composer/bin/composer

fresh:
	$(PHP) artisan migrate:fresh

ready:
	make paratest
	make phpstan

seed:
	$(PHP) artisan app:fetch-clips-command --startedAt=2023-01

phpstan:
	$(PHP) vendor/bin/phpstan analyse

up:
	$(COMPOSER) up

install:
	$(COMPOSER) install

test:
	$(PHP) vendor/bin/phpunit $(arg)

paratest:
	$(PHP) vendor/bin/paratest --processes=5 --runner WrapperRunner

fetch-clips:
	$(PHP) artisan app:fetch-clips-command

update-clips:
	$(PHP) artisan app:update-clips-command

regularize-games:
	$(PHP) artisan app:regularize-games-created-at

algolia-flush:
	$(PHP) artisan scout:flush "App\Models\Clip"
	$(PHP) artisan scout:flush "App\Models\Game"
	$(PHP) artisan queue:work --queue=algolia --stop-when-empty

algolia-import:
	$(PHP) artisan scout:import "App\Models\Clip"
	$(PHP) artisan scout:import "App\Models\Game"
	$(PHP) artisan queue:work --queue=algolia --stop-when-empty

bigbang:
	$(PHP) artisan migrate:fresh
	$(PHP) artisan app:fetch-clips-command --startedAt=2023-01
	$(PHP) artisan queue:work --queue=fetch-clip --stop-when-empty
	$(PHP) artisan queue:work --queue=finalize-game --stop-when-empty
	$(PHP) artisan queue:work --queue=algolia --stop-when-empty
