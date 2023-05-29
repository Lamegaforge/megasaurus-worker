<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ValueObjects\FetchedGame;
use App\Jobs\UpdateGameFromFetchedGameJob;
use Domain\Models\Game;
use App\Actions\FetchGamesFromExternalIds;

class FetchGamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-games-command';

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
        Game::whereNull('name')
            ->chunkById(100, function ($games) {

                $externalIdList = $games->pluck('external_id');

                $fetchedGames = app(FetchGamesFromExternalIds::class)->handle($externalIdList);

                $fetchedGames->map(function (FetchedGame $fetchedGame) {
                    UpdateGameFromFetchedGameJob::dispatch($fetchedGame)->onQueue('fetch-game');
                });
            });

        return SELF::SUCCESS;
    }
}
