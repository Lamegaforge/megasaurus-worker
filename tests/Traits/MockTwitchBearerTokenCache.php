<?php

namespace Tests\Traits;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\ValueObjects\BearerToken;

trait MockTwitchBearerTokenCache
{
    protected function mockTwitchBearerTokenCache(): void
    {
        Cache::shouldReceive('remember')
            ->with('twitch_bearer_token', 10800, Closure::class)
            ->andReturn(new BearerToken('...'));
    }
}
