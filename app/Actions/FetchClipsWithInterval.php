<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\ValueObjects\Interval;
use App\ValueObjects\FetchedClip;
use Illuminate\Support\Collection;
use App\Storages\TwitchBearerTokenStorage;
use Illuminate\Http\Client\Response;

class FetchClipsWithInterval
{
    public function __construct(
        private TwitchBearerTokenStorage $twitchBearerTokenStorage,
    ) {}

    /**
     * @see https://dev.twitch.tv/docs/api/reference/#get-clips
     */
    public function handle(Interval $interval): Collection
    {
        $url = 'https://api.twitch.tv/helix/clips'
            . '?broadcaster_id=' . config('twitch.broadcaster.id')
            . '&first=100'
            . '&started_at=' . $interval->getStartedAt()
            . '&ended_at=' . $interval->getendedAt();

        $responses = $this->callHttpClient($url);

        return $responses
            ->collect('data')
            ->map(function ($attributes) {
                return FetchedClip::from($attributes);
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
