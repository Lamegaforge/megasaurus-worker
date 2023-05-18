<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Storages\TwitchBearerTokenStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Http::macro('helix', function () {

            $bearerToken = app(TwitchBearerTokenStorage::class)->get();
            
            $headers = [
                'Client-Id' => config('twitch.client.id'),
            ];

            return Http::withHeaders($headers)
                ->withToken($bearerToken->value)
                ->baseUrl('https://api.twitch.tv/helix/');
        });
    }
}
