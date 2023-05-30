<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ValueObjects\ExternalId;
use App\Actions\UpdateGameFromFetchedGame;
use App\Actions\SaveCardToSpace;
use App\Actions\FetchGameFromExternalId;
use Domain\Models\Game;

class FinalizeGameCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $gameId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $game = Game::find($this->gameId);

        $externalId = new ExternalId($game->external_id);

        $fetchedGame = app(FetchGameFromExternalId::class)->handle($externalId);

        app(UpdateGameFromFetchedGame::class)->handle($fetchedGame);

        app(SaveCardToSpace::class)->handle(
            externalId: $externalId,
            card: $fetchedGame->card,
        );
    }

    public function uniqueId(): int
    {
        return $this->gameId;
    }
}
