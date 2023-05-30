<?php

namespace App\Actions;

use Domain\Models\Clip;
use Domain\Models\Author;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\FetchedAuthor;
use App\Services\SuspiciousClipDetector;
use App\Actions\StoreGameFromFetchedClip;

class StoreFetchedClip
{
    public function __construct(
        private SuspiciousClipDetector $suspiciousClipDetector,
        private StoreGameFromFetchedClip $storeGameFromFetchedClip,
    ) {}

    public function handle(FetchedClip $fetchedClip): Clip
    {
        $author = $this->retrieveOrCreateAuthor($fetchedClip->author);

        $game = $this->storeGameFromFetchedClip->handle($fetchedClip);

        $clip = $this->retrieveOrCreateClip($fetchedClip);

        $clip->author()->associate($author);

        $clip->game()->associate($game);

        $clip->save();

        return $clip;
    }

    private function retrieveOrCreateAuthor(FetchedAuthor $fetchedAuthor): Author
    {
        return Author::firstOrCreate([
            'external_id' => $fetchedAuthor->externalId,
        ], [
            'name' => $fetchedAuthor->name,
        ]);
    }

    private function retrieveOrCreateClip(FetchedClip $fetchedClip): Clip
    {
        $state = $this->suspiciousClipDetector->fromFetchedClip($fetchedClip);

        return Clip::firstOrNew([
            'external_id' => $fetchedClip->externalId,
        ], [
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
