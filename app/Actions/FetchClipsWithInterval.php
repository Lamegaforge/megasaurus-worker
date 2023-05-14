<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\BearerToken;
use App\ValueObjects\Interval;
use App\ValueObjects\FetchedClip;
use Illuminate\Support\Collection;

class FetchClipsWithInterval
{
    public function handle(BearerToken $bearerToken, Interval $interval): Collection
    {
        $url = 'https://api.twitch.tv/helix/clips'
            . '?broadcaster_id=' . config('twitch.broadcaster.id')
            . '&first=100'
            . '&started_at=' . $interval->getStartedAt()
            . '&ended_at=' . $interval->getendedAt();

        $responses = Http::withToken($bearerToken->value)
            ->withHeaders([
                'Client-Id' => config('twitch.client.id'),
            ])
            ->get($url);

        return $responses
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
            });
    }
}
