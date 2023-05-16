<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ValueObjects\FetchedGame;
use App\Actions\StoreFetchedGame;
use App\Actions\SaveCardToSpace;

class StoreFetchedGameJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public FetchedGame $fetchedGame,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(StoreFetchedGame::class)->handle($this->fetchedGame);

        app(SaveCardToSpace::class)->handle(
            card: $this->fetchedGame->card,
        );
    }

    public function uniqueId(): string
    {
        return $this->fetchedGame->external_id;
    }
}
