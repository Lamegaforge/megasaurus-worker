<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\ExternalId;

class FetchClipFromExternalId
{
    public function handle(ExternalId $externalId): FetchedClip
    {
        $url = 'clips?id=' . $externalId;

        $response = Http::helix()->get($url);

        return $response
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
            })
            ->first();
    }
}
