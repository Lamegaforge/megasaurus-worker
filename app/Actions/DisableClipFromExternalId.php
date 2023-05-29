<?php

namespace App\Actions;

use Domain\Models\Clip;
use Domain\Enums\ClipStateEnum;

class DisableClipFromExternalId
{
    public function handle(string $externalId): void
    {
        Clip::where('external_id', $externalId)
            ->update([
                'state' => ClipStateEnum::Disable,
            ]);
    }
}
