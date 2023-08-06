<?php

namespace App\Services;

use App\ValueObjects\FetchedClip;
use App\ValueObjects\SuspiciousBag;
use Domain\Enums\ClipStateEnum;
use App\Pipelines\SuspiciousTitle;
use Illuminate\Pipeline\Pipeline;

class SuspiciousClipDetector
{
    public function fromFetchedClip(FetchedClip $fetchedClip): ClipStateEnum
    {
        $suspiciousBag = app(Pipeline::class)
            ->send(new SuspiciousBag(
                ClipStateEnum::Ok,
                $fetchedClip->title,
                $fetchedClip->duration,
            ))
            ->through([
                SuspiciousTitle::class,
            ])
            ->thenReturn();
        
        return $suspiciousBag->state;
    }
}