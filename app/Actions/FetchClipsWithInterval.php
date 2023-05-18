<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\Interval;
use App\ValueObjects\FetchedClip;
use Illuminate\Support\Collection;

class FetchClipsWithInterval
{
    /**
     * @see https://dev.twitch.tv/docs/api/reference/#get-clips
     */
    public function handle(Interval $interval): Collection
    {
        $url = 'clips'
            . '?broadcaster_id=' . config('twitch.broadcaster.id')
            . '&first=100'
            . '&started_at=' . $interval->getStartedAt()
            . '&ended_at=' . $interval->getEndedAt();

        $responses = Http::helix()->get($url);

        return $responses
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
            });
    }
}
