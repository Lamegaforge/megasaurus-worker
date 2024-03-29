<?php

namespace App\Pipelines;

use Closure;
use Domain\Enums\ClipStateEnum;
use App\ValueObjects\SuspiciousBag;

class SuspiciousTitle
{
    public function handle(SuspiciousBag $bag, Closure $next): SuspiciousBag
    {
        if (preg_match('#.*\s?\｢.*\｣#', $bag->title)) {
            $bag->state = ClipStateEnum::Suspicious;
        }

        return $next($bag);
    }
}
