<?php

namespace App\Actions;

use App\Models\Clip;
use App\ValueObjects\FetchedClip;
use App\Services\SuspiciousClipDetector;

class UpdateClipFromFetchedClip
{
    public function __construct(
        private SuspiciousClipDetector $suspiciousClipDetector,
    ) {}

    public function handle(FetchedClip $fetchedClip): void
    {
        $state = $this->suspiciousClipDetector->fromFetchedClip($fetchedClip);

        /**
         * Using the update method does not trigger Algolia persistence.
         * This forces us to use the save method.
         */
        $clip = Clip::where('external_id', $fetchedClip->externalId)->first();

        $clip->fill([
            'title' => $fetchedClip->title,
            'views' => $fetchedClip->views,
            'state' => $state,
        ]);

        $clip->save();
    }
}
