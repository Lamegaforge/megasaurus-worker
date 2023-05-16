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
        $externalGameIdList = Clip::doesntHave('game')
            ->pluck('external_game_id')
            ->unique();

        $fetchedGames = app(FetchGamesFromExternalIds::class)->handle($externalGameIdList);

        $fetchedGames->map(function (FetchedGame $fetchedGame) {
            StoreFetchedGameJob::dispatch($fetchedGame)->onQueue('fetch-game');
        });

        return SELF::SUCCESS;
    }
}
