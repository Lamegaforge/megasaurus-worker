<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedGame;
use Illuminate\Support\Collection;

class FetchGamesFromExternalIds
{
    public function handle(Collection $externalId): Collection
    {
        $queryString = $externalId->map(function ($item) {
            return 'id=' . $item;
        })->implode('&');

        $url = 'games?' . $queryString;

        $response = Http::helix()->get($url);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedGame::from($attributes);
            });
    }
}
