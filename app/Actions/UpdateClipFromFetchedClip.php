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

        Clip::where('external_id', $fetchedClip->external_id)
            ->update([
                'title' => $fetchedClip->title,
                'views' => $fetchedClip->views,
                'state' => $state,
            ]);
    }
}
