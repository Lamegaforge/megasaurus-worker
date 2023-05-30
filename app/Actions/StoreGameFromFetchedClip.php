<?php

namespace App\Actions;

use Domain\Models\Game;
use App\ValueObjects\FetchedClip;
use App\ValueObjects\ExternalId;
use App\Jobs\FinalizeGameCreationJob;

class StoreGameFromFetchedClip
{
    public function handle(FetchedClip $fetchedClip): Game
    {
        $externalGameId = $fetchedClip->externalGameId;

        /** 
         * sometimes, clip is not attached to a game
         */
        return is_null($externalGameId) 
            ? $this->getDefaultGame() 
            : $this->firstOrCreateGame($externalGameId) ;
    }

    /**
     * In this situation, we make a dummy game
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
        $game = Game::firstOrCreate([
            'external_id' => $externalId,
        ], []);

        /**
         * if the game has just been created
         * we need to get his name and card image from another API
         */
        if ($game->wasRecentlyCreated) {
            FinalizeGameCreationJob::dispatch($externalId)->onQueue('finalize-game');
        }

        return $game;
    }
}
