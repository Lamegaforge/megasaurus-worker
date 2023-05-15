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
use Illuminate\Database\QueryException;

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
        $this->storeFetchedClip();
        $this->saveThumbnailToSpace();
    }

    public function uniqueId(): string
    {
        return $this->fetchedClip->external_id;
    }

    private function storeFetchedClip(): void
    {
        try {
            app(StoreFetchedClip::class)->handle($this->fetchedClip);
        } catch (QueryException $exception) {

            // "Integrity Constraint Violation" errors are ignored 
            // to allow failed jobs to be retried
            if ($exception->getCode() != '23000') {
                throw $exception;
            }
        }
    }

    private function saveThumbnailToSpace(): void
    {
        app(SaveThumbnailToSpace::class)->handle(
            thumbnail: $this->fetchedClip->thumbnail,
        );
    }
}
