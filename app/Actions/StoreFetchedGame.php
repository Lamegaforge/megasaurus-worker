<?php

namespace App\Actions;

use App\Models\Game;
use App\ValueObjects\FetchedGame;

class StoreFetchedGame
{
    public function handle(FetchedGame $fetchedGame): Game
    {
        $game = Game::firstOrCreate([
            'external_id' => $fetchedGame->external_id,
        ], [
            'name' => $fetchedGame->name,
        ]);
        
        return $game;
    }
}
