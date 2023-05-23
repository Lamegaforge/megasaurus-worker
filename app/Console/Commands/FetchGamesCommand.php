<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ValueObjects\FetchedGame;
use App\Jobs\StoreFetchedGameJob;
use App\Models\Clip;
use App\Actions\FetchGamesFromExternalIds;

class FetchGamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-games-command';

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
        /** @phpstan-ignore-next-line */
        Clip::doesntHave('game')
            ->whereNotNull('external_game_id')
            ->distinct('external_game_id')
            ->chunkById(100, function ($clips) {

                $externalIdList = $clips->pluck('external_game_id');

                $fetchedGames = app(FetchGamesFromExternalIds::class)->handle($externalIdList);

                $fetchedGames->map(function (FetchedGame $fetchedGame) {
                    StoreFetchedGameJob::dispatch($fetchedGame)->onQueue('fetch-game');
                });
            });

        return SELF::SUCCESS;
    }
}
