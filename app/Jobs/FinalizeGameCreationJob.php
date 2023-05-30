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

class FinalizeGameCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ExternalId $externalId,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fetchedGame = $this->updateGameMissingProperty();

        $this->saveCardToSpace($fetchedGame);
    }

    private function updateGameMissingProperty(): FetchedGame
    {
        $fetchedGame = app(FetchGameFromExternalId::class)->handle($this->externalId);

        app(UpdateGameFromFetchedGame::class)->handle($fetchedGame);

        return $fetchedGame;
    }

    private function saveCardToSpace(FetchedGame $fetchedGame): void
    {
        app(SaveCardToSpace::class)->handle(
            externalId: $this->externalId,
            card: $fetchedGame->card,
        );
    }

    public function uniqueId(): string
    {
        return $this->externalId->value;
    }
}
