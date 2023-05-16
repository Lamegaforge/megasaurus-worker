fresh:
	/usr/local/opt/php@8.2/bin/php artisan migrate:fresh

phpstan:
	vendor/bin/phpstan analyse

up:
	valet composer up

fetch-clips:
	/usr/local/opt/php@8.2/bin/php artisan app:fetch-clips-command

fetch-games:
	/usr/local/opt/php@8.2/bin/php artisan app:fetch-games-command

octane-start:
	/usr/local/opt/php@8.2/bin/php artisan octane:start --watch