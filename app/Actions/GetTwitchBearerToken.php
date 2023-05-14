<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\BearerToken;

class GetTwitchBearerToken
{
    public function handle(): BearerToken
    {
        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'client_id' => config('twitch.client.id'),
            'client_secret' => config('twitch.client.secret'),
            'grant_type' => 'client_credentials',
        ]);

        return new BearerToken(
            value: $response->json('access_token'),
        );
    }
}
