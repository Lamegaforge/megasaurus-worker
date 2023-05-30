<?php

namespace App\Actions;

use Domain\Models\Clip;
use Domain\Enums\ClipStateEnum;
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
