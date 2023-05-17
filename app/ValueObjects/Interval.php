<?php

namespace App\ValueObjects;

use Carbon\Carbon;

readonly final class Interval
{
    public function __construct(
        private Carbon $startedAt,
        private Carbon $endedAt,
    ) {}

    public static function last24Hours(): self
    {
        return new self(
            startedAt: Carbon::now()->subDay(),
            endedAt: Carbon::now(),
        );
    }

    public static function last48Hours(): self
    {
        return new self(
            startedAt: Carbon::now()->subDays(2),
            endedAt: Carbon::now(),
        );
    }

    public static function wholeMonthSince(string $startedAt): self
    {
        $startedAt = Carbon::parse($startedAt);

        return new self(
            startedAt: $startedAt,
            endedAt: $startedAt->clone()->endOfMonth(),
        );
    }

    public function getStartedAt(): string
    {
        return $this->startedAt->toIso8601ZuluString();
    }

    public function getEndedAt(): string
    {
        return $this->endedAt->toIso8601ZuluString();
    }
}
