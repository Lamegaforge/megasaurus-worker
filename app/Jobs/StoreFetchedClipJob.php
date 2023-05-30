<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ValueObjects\FetchedClip;
use App\Actions\StoreFetchedClip;
use App\Actions\SaveThumbnailToSpace;

class StoreFetchedClipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public FetchedClip $fetchedClip,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(StoreFetchedClip::class)->handle($this->fetchedClip);

        app(SaveThumbnailToSpace::class)->handle(
            thumbnail: $this->fetchedClip->thumbnail,
        );
    }

    public function uniqueId(): string
    {
        return $this->fetchedClip->externalId;
    }
}
