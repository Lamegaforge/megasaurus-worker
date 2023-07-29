<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\FetchClipFromExternalId;
use App\Actions\FetchGameFromExternalId;
use App\Models\Clip;
use App\ValueObjects\ExternalId;

class DebugClipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-clip-command {hook}';

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
        $hook = $this->argument('hook');

        $clip = Clip::where('uuid', $hook)
            ->orWhere('external_id', $hook)
            ->firstOrFail();

        $fetchedClip = app(FetchClipFromExternalId::class)->handle(
            new ExternalId($clip->external_id),
        );

        $fetchedGame = app(FetchGameFromExternalId::class)->handle(
            new ExternalId($clip->external_game_id),
        );

        $this->table(
            ['Property', 'Value'],
            [
                ['External Id', $fetchedClip->externalId->value],
                ['External Game Id', $fetchedClip->externalGameId->value],
                ['Title', $fetchedClip->title],
                ['Author External Id', $fetchedClip->author->externalId->value],
                ['Author Name', $fetchedClip->author->name],
                ['URL', $fetchedClip->url],
                ['Thumbnail', $fetchedClip->thumbnail->url],
                ['Views', $fetchedClip->views],
                ['Duration', $fetchedClip->duration],
                ['Published At', $fetchedClip->published_at],
            ]
        );

        $this->table(
            ['Property', 'Value'],
            [
                ['External Id', $fetchedGame->external_id],
                ['Name', $fetchedGame->name],
                ['Card', $fetchedGame->card->url],
            ]
        );

        return SELF::SUCCESS;
    }
}
