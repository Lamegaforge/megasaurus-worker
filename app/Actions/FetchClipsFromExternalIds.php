<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedClip;
use Illuminate\Support\Collection;

class FetchClipsFromExternalIds
{
    public function handle(Collection $externalIdList): Collection
    {
        $queryString = $externalIdList->map(function ($externalId) {
            return 'id=' . $externalId;
        })->implode('&');

        $response = Http::helix()->get('clips?' . $queryString);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
            });
    }
}
