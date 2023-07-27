<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Game;

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
    public function handle(): int
    {
        /** @phpstan-ignore-next-line */
        $games = Game::with(['clips' => function ($query) {
            $query->oldest('published_at');
        }])->get();

        $this->withProgressBar($games, function (Game $game) {

            /** @phpstan-ignore-next-line */
            $clip = $game->clips->first();

            $game->update([
                'created_at' => $clip->published_at,
            ]);
        });

        return SELF::SUCCESS;
    }
}
