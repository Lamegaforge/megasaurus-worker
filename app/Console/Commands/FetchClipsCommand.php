<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\FetchClipsWithInterval;
use App\ValueObjects\Interval;
use App\ValueObjects\FetchedClip;
use App\Jobs\StoreFetchedClipJob;
use App\Storages\TwitchBearerTokenStorage;
use App\Models\Clip;
use Illuminate\Support\Collection;

class FetchClipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-clips-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    private ?Collection $clipsAlreadySaved = null;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bearerToken = app(TwitchBearerTokenStorage::class)->get();

        $fetchedClips = app(FetchClipsWithInterval::class)->handle(
            bearerToken: $bearerToken,
            interval: Interval::last48Hours(),
        );

        $fetchedClips
            ->reject(fn (FetchedClip $fetchedClip) => $this->alreadyStore($fetchedClip))
            ->map(function (FetchedClip $fetchedClip) {
                StoreFetchedClipJob::dispatch($fetchedClip)->onQueue('fetch-clip');
            });

        return SELF::SUCCESS;
    }

    private function alreadyStore(FetchedClip $fetchedClip): bool
    {
        $this->clipsAlreadySaved ??= $this->getClipsAlreadySaved();

        return $this->clipsAlreadySaved->contains($fetchedClip->external_id);
    }

    private function getClipsAlreadySaved(): Collection
    {
        $clips = Clip::select('external_id')->get();

        return $clips->pluck('external_id');
    }
}
