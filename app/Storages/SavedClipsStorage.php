<?php

namespace App\Storages;

use App\Actions\GetTwitchBearerToken;
use App\ValueObjects\BearerToken;
use Illuminate\Support\Facades\Cache;

class TwitchBearerTokenStorage
{
    public function get(): BearerToken
    {
        return Cache::remember('twitch_bearer_token', TtlFactory::hours(3), function () {
            return app(GetTwitchBearerToken::class)->handle();
        });
    }
}
