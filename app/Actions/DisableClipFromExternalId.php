<?php

namespace App\Actions;

use App\Models\Clip;
use Domain\Enums\ClipStateEnum;
use App\ValueObjects\ExternalId;

class DisableClipFromExternalId
{
    public function handle(ExternalId $externalId): void
    {
        /**
         * Using the update method does not trigger Algolia persistence.
         * This forces us to use the save method.
         */
        $clip = Clip::where('external_id', $externalId)->first();

        $clip->state = ClipStateEnum::Disable;

        $clip->save();
    }
}
