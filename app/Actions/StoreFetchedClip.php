<?php

namespace App\Actions;

use App\Models\Clip;
use App\ValueObjects\FetchedClip;
use App\Services\SuspiciousClipDetector;
use App\Actions\StoreGameFromFetchedClip;
use App\Actions\StoreAuthorFromFetchedAuthor;
use Illuminate\Support\Str;

class StoreFetchedClip
{
    public function __construct(
        private SuspiciousClipDetector $suspiciousClipDetector,
        private StoreAuthorFromFetchedAuthor $storeAuthorFromFetchedAuthor,
        private StoreGameFromFetchedClip $storeGameFromFetchedClip,
    ) {}

    public function handle(FetchedClip $fetchedClip): Clip
    {
        $author = $this->storeAuthorFromFetchedAuthor->handle($fetchedClip->author);

        $game = $this->storeGameFromFetchedClip->handle($fetchedClip);

        $clip = $this->retrieveOrCreateClip($fetchedClip);

        $clip->author()->associate($author);

        $clip->game()->associate($game);

        $clip->save();

        return $clip;
    }

    private function retrieveOrCreateClip(FetchedClip $fetchedClip): Clip
    {
        $state = $this->suspiciousClipDetector->fromFetchedClip($fetchedClip);

        return Clip::firstOrNew([
            'external_id' => $fetchedClip->externalId,
        ], [
            'uuid' => (string) Str::uuid(),
            'external_game_id' => $fetchedClip->externalGameId,
            'url' => $fetchedClip->url,
            'title' => $fetchedClip->title,
            'views' => $fetchedClip->views,
            'duration' => $fetchedClip->duration,
            'state' => $state,
            'published_at' => $fetchedClip->published_at,
        ]);
    }
}
