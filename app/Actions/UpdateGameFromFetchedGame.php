<?php

namespace App\Actions;

use Domain\Models\Game;
use App\ValueObjects\FetchedGame;

class UpdateGameFromFetchedGame
{
    public function handle(Game $game, FetchedGame $fetchedGame): void
    {
        $game->update([
            'name' => $fetchedGame->name,
        ]);
    }
}
