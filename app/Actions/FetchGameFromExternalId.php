<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedGame;
use App\ValueObjects\ExternalId;

class FetchGameFromExternalId
{
    public function handle(ExternalId $externalId): FetchedGame
    {
        $url = 'games?id=' . $externalId;

        $response = Http::helix()->get($url);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedGame::from($attributes);
            })
            ->first();
    }
}
