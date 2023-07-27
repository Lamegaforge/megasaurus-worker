<?php

namespace App\Pipelines;

use Closure;
use App\Enums\ClipStateEnum;
use App\ValueObjects\SuspiciousBag;

class SuspiciousDuration
{
    public function handle(SuspiciousBag $bag, Closure $next): SuspiciousBag
    {
        if ($bag->duration === 30) {
            $bag->state = ClipStateEnum::Suspicious;
        }

        return $next($bag);
    }
}
