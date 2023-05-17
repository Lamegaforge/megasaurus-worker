<?php

namespace App\Actions;

use App\Models\Clip;
use App\Models\Author;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\FetchedAuthor;
use App\Services\SuspiciousClipDetector;

class StoreFetchedClip
{
    public function __construct(
        private SuspiciousClipDetector $suspiciousClipDetector,
    ) {}

    public function handle(FetchedClip $fetchedClip): Clip
    {
        $author = $this->retrieveOrCreateAuthor($fetchedClip->author);

        $clip = $this->retrieveOrCreateClip($fetchedClip);

        $clip->author()->associate($author);

        $clip->save();

        return $clip;
    }

    private function retrieveOrCreateAuthor(FetchedAuthor $fetchedAuthor): Author
    {
        return Author::firstOrCreate([
            'external_id' => $fetchedAuthor->external_id,
        ], [
            'name' => $fetchedAuthor->name,
        ]);
    }

    private function retrieveOrCreateClip(FetchedClip $fetchedClip): Clip
    {
        $state = $this->suspiciousClipDetector->fromFetchedClip($fetchedClip);

        return Clip::firstOrNew([
            'external_id' => $fetchedClip->external_id,
        ], [
            'external_game_id' => $fetchedClip->external_game_id,
            'url' => $fetchedClip->url,
            'title' => $fetchedClip->title,
            'views' => $fetchedClip->views,
            'duration' => $fetchedClip->duration,
            'state' => $state,
            'published_at' => $fetchedClip->published_at,
        ]);
    }
}
