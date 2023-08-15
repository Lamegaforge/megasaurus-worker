<?php

namespace App\Console\Commands;

use App\Models\Game;
use Domain\Enums\ClipStateEnum;
use Illuminate\Console\Command;

class UpdateGamesActiveClipCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-games-active-clip-count-command';

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
        Game::with('activeClips')
            ->each(function ($game) {
                $game->update([
                    'active_clip_count' => $game->activeClips->count(),
                ]);
            });

        return SELF::SUCCESS;
    }
}
