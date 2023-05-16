<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedGame;
use Illuminate\Support\Collection;
use App\Storages\TwitchBearerTokenStorage;
use Illuminate\Http\Client\Response;

class FetchGamesFromExternalIds
{
    public function __construct(
        private TwitchBearerTokenStorage $twitchBearerTokenStorage,
    ) {}

    public function handle(Collection $externalId): Collection
    {
        $queryString = $externalId->map(function ($item) {
            return 'id=' . $item;
        })->implode('&');

        $url = 'https://api.twitch.tv/helix/games?' . $queryString;

        $response = $this->callHttpClient($url);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedGame::from($attributes);
            });
    }

    private function callHttpClient(string $url): Response
    {
        $bearerToken = $this->twitchBearerTokenStorage->get();

        return Http::withToken($bearerToken->value)
            ->withHeaders([
                'Client-Id' => config('twitch.client.id'),
            ])
            ->get($url);
    }
}
