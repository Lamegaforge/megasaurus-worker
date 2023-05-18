<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedClip;
use Illuminate\Support\Collection;

class FetchClipsFromExternalIds
{
    public function handle(Collection $externalId): Collection
    {
        $queryString = $externalId->map(function ($item) {
            return 'id=' . $item;
        })->implode('&');

        $response = Http::helix()->get('clips?' . $queryString);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
            });
    }
}
