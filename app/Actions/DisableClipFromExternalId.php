<?php

namespace App\Actions;

use App\Models\Clip;
use App\Enums\ClipStateEnum;
use App\ValueObjects\ExternalId;

class DisableClipFromExternalId
{
    public function handle(ExternalId $externalId): void
    {
        Clip::where('external_id', $externalId)
            ->update([
                'state' => ClipStateEnum::Disable,
            ]);
    }
}
