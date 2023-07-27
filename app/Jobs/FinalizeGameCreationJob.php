<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ValueObjects\ExternalId;
use App\ValueObjects\FetchedGame;
use App\Actions\UpdateGameFromFetchedGame;
use App\Actions\SaveCardToSpace;
use App\Actions\FetchGameFromExternalId;
use App\Models\Game;

class FinalizeGameCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $uuid,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $game = Game::where('uuid', $this->uuid)->firstOrFail();

        $fetchedGame = app(FetchGameFromExternalId::class)->handle(
            new ExternalId($game->external_id),
        );

        $this->updateGameMissingProperty($game, $fetchedGame);

        $this->saveCardToSpace($game, $fetchedGame);
    }

    private function updateGameMissingProperty(Game $game, FetchedGame $fetchedGame): void
    {
        app(UpdateGameFromFetchedGame::class)->handle($game, $fetchedGame);
    }

    private function saveCardToSpace(Game $game, FetchedGame $fetchedGame): void
    {
        app(SaveCardToSpace::class)->handle(
            game: $game,
            card: $fetchedGame->card,
        );
    }

    public function uniqueId(): string
    {
        return $this->uuid;
    }
}
