<?php

namespace App\Actions;

use App\Models\Clip;
use App\Enums\ClipStateEnum;

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
