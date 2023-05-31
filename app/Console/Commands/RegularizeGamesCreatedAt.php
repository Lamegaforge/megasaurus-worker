<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $games = Game::with(['clips' => function ($query) {
            $query->oldest('published_at');
        }])->get();

        $this->withProgressBar($games, function (Game $game) {

            $clip = $game->clips->first();

            $game->update([
                'created_at' => $clip->published_at,
            ]);
        });

        return SELF::SUCCESS;
    }
}
