<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\FetchClipsWithInterval;
use App\ValueObjects\Interval;
use App\ValueObjects\FetchedClip;
use App\Jobs\StoreFetchedClipJob;
use App\Models\Clip;
use Illuminate\Support\Collection;

class FetchClipsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-clips-command {--startedAt=}';

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
        $interval = $this->getFetchInterval();

        $fetchedClips = app(FetchClipsWithInterval::class)->handle($interval);

        $fetchedClips->map(function (FetchedClip $fetchedClip) {
            StoreFetchedClipJob::dispatch($fetchedClip)->onQueue('fetch-clip');
        });

        return SELF::SUCCESS;
    }

    private function getFetchInterval(): Interval
    {
        $startedAt = $this->option('startedAt');

        return $startedAt 
            ? Interval::wholeMonthSince($startedAt)
            : Interval::lastHour();
    }
}
