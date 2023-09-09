# megasaurus-worker
## Twitch
https://dev.twitch.tv/console/apps
```
TWITCH_CLIENT_ID=
TWITCH_CLIENT_SECRET=
```
## Digital Ocean Space
```
DIGITALOCEAN_SPACES_KEY=
DIGITALOCEAN_SPACES_SECRET=
DIGITALOCEAN_SPACES_ENDPOINT=
DIGITALOCEAN_SPACES_REGION=
DIGITALOCEAN_SPACES_BUCKET=
DIGITALOCEAN_SPACES_ROOT=
```
## Sentry
```
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=
```
## Algolia & Scout

### Dev
- [Algolia Dashboard](https://dashboard.algolia.com/apps/TQ46K0LZKJ/dashboard)
- [Algolia Api keys](https://dashboard.algolia.com/account/api-keys/all?applicationId=TQ46K0LZKJ)

### Prod
- [Algolia Dashboard](https://dashboard.algolia.com/apps/PII9W2GXJ8/dashboard)
- [Algolia Api keys](https://dashboard.algolia.com/account/api-keys/all?applicationId=PII9W2GXJ8)

```
SCOUT_DRIVER=algolia
SCOUT_QUEUE=true

ALGOLIA_APP_ID=
ALGOLIA_SECRET=
```

```
php artisan scout:flush "App\Models\Clip"
php artisan scout:flush "App\Models\Game"
```
```
php artisan scout:import "App\Models\Clip"
php artisan scout:import "App\Models\Game"
```
