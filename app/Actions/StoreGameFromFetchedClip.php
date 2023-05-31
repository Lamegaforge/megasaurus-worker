<?php

namespace App\Actions;

use Domain\Models\Game;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\ExternalId;
use App\Jobs\FinalizeGameCreationJob;
use Illuminate\Support\Facades\Cache;

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
        ], []);
    }

    private function firstOrCreateGame(ExternalId $externalId): Game
    {
        $lockName = $this->getLockName($externalId);

        /** 
         * use of the lock to avoid duplication constraints during a game creation
         * that can be caused by multiple workers
         */
        $game = Cache::lock($lockName, 3)->get(function () use ($externalId) {
            return Game::firstOrCreate([
                'external_id' => $externalId,
            ], []);
        });

        /**
         * if the game has just been created
         * we need to get his name and card image from another API
         */
        if ($game->wasRecentlyCreated) {
            FinalizeGameCreationJob::dispatch($externalId)->onQueue('finalize-game');
        }

        return $game;
    }

    private function getLockName(ExternalId $externalId): string
    {
        return 'store-game-' . $externalId->value;
    }
}
