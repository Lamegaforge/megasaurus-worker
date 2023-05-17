<?php

namespace App\Pipelines;

use Closure;
use App\Enums\ClipStateEnum;
use App\valueObjects\SuspiciousBag;

class SuspiciousTitle
{
    public function handle(SuspiciousBag $bag, Closure $next)
    {
        if (preg_match('#.*\s?\｢.*\｣#', $bag->title)) {
            $bag->state = ClipStateEnum::Suspicious;
        }

        return $next($bag);
    }
}
