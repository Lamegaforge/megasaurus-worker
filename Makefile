fresh:
	/usr/local/opt/php@8.2/bin/php artisan migrate:fresh

phpstan:
	vendor/bin/phpstan analyse

up:
	valet composer up

test:
	/usr/local/opt/php@8.2/bin/php vendor/bin/phpunit

fetch-clips:
	/usr/local/opt/php@8.2/bin/php artisan app:fetch-clips-command

fetch-games:
	/usr/local/opt/php@8.2/bin/php artisan app:fetch-games-command

update-clips:
	/usr/local/opt/php@8.2/bin/php artisan app:update-clips-command

octane-start:
	/usr/local/opt/php@8.2/bin/php artisan octane:start --watch