phpstan:
	vendor/bin/phpstan analyse

up:
	valet composer up

fetch-clips:
	/usr/local/opt/php@8.2/bin/php artisan app:fetch-clips-command

octane-start:
	/usr/local/opt/php@8.2/bin/php artisan octane:start --watch