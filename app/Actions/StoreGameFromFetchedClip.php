<?php

namespace App\Actions;

use App\Models\Game;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\ExternalId;
use App\Jobs\FinalizeGameCreationJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StoreGameFromFetchedClip
{
    public function handle(FetchedClip $fetchedClip): Game
    {
        $externalGameId = $fetchedClip->externalGameId;

        /** 
         * sometimes, clip is not attached to a game
         * this happens when a game is disabled on twitch
         */
        return is_null($externalGameId) 
            ? $this->getDefaultGame() 
            : $this->firstOrCreateGame($externalGameId) ;
    }

    /**
     * in this situation, we make a dummy game
     */
    private function getDefaultGame(): Game
    {
        return Game::firstOrCreate([
            'external_id' => 'nowhere',
            'name' => 'nowhere',
        ], [
            'uuid' => (string) Str::uuid(),
        ]);
    }

    private function firstOrCreateGame(ExternalId $externalId): Game
    {
        $lockName = $this->getLockName($externalId);

        /** 
         * use of the lock to avoid duplication constraints during a game creation
         * that can be caused by multiple workers
         */
        $game = Cache::lock($lockName, 3)->block(2, function () use ($externalId) {
            return Game::firstOrCreate([
                'external_id' => $externalId,
            ], [
                'uuid' => (string) Str::uuid(),
            ]);
        });

        /**
         * if the game has just been created
         * we need to get his name and card image from another API
         */
        if ($game->wasRecentlyCreated) {
            FinalizeGameCreationJob::dispatch($game->uuid)->onQueue('finalize-game');
        }

        return $game;
    }

    private function getLockName(ExternalId $externalId): string
    {
        return 'store-game-' . $externalId->value;
    }
}
