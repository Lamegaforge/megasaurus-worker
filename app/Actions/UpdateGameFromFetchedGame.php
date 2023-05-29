<?php

namespace App\Actions;

use Domain\Models\Game;
use App\ValueObjects\FetchedGame;

class UpdateGameFromFetchedGame
{
    public function handle(FetchedGame $fetchedGame): void
    {
        Game::where('external_id', $fetchedGame->external_id)
            ->update([
                'name' => $fetchedGame->name,
            ]);
    }
}
