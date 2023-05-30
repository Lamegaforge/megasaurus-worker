<?php

namespace App\Actions;

use Domain\Models\Clip;
use Domain\Models\Author;
use Domain\Models\Game;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\FetchedAuthor;
use App\ValueObjects\ExternalId;
use App\Services\SuspiciousClipDetector;
use App\Jobs\FinalizeGameCreationJob;

class StoreFetchedClip
{
    public function __construct(
        private SuspiciousClipDetector $suspiciousClipDetector,
    ) {}

    public function handle(FetchedClip $fetchedClip): Clip
    {
        $author = $this->retrieveOrCreateAuthor($fetchedClip->author);

        $game = $this->retrieveOrCreateGame($fetchedClip);

        $clip = $this->retrieveOrCreateClip($fetchedClip);

        $clip->author()->associate($author);

        $clip->game()->associate($game);

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

    private function retrieveOrCreateGame(FetchedClip $fetchedClip): Game
    {
        $game = Game::firstOrCreate([
            'external_id' => $fetchedClip->external_game_id,
        ], []);

        if ($game->wasRecentlyCreated) {
            FinalizeGameCreationJob::dispatch(
                new ExternalId($fetchedClip->external_game_id),
            )->onQueue('finalize-game');
        }

        return $game;
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
