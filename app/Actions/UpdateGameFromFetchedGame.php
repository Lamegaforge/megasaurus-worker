<?php

namespace App\Actions;

use App\Models\Game;
use App\ValueObjects\FetchedGame;

class UpdateGameFromFetchedGame
{
    public function handle(Game $game, FetchedGame $fetchedGame): void
    {
        /**
         * Using the update method does not trigger Algolia persistence.
         * This forces us to use the save method.
         */
        $game->name = $fetchedGame->name;

        $game->save();
    }
}
