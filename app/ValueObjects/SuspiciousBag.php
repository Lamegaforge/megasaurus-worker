<?php

namespace App\ValueObjects;

use App\Enums\ClipStateEnum;

final class SuspiciousBag
{
    public function __construct(
        public ClipStateEnum $state,
        public string $title,
        public int $duration,
    ) {
    }
}
