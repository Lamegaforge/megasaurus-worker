<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Domain\Models\Clip;
use Domain\Models\Game;

class RegularizeGamesCreatedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:regularize-games-created-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->withProgressBar(Game::all(), function (Game $game) {

            $clip = Clip::where('external_game_id', $game->external_id)
                ->oldest('published_at')
                ->first();

            $game->update([
                'created_at' => $clip->published_at,
            ]);
        });

        return SELF::SUCCESS;
    }
}
