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
        /**
         * we're adding 10 minutes to avoid a strange behaviour of the twitch api.
         * recent clip does not appear immediately in the api data.
         */
        return $this->endedAt->addMinutes(10)->toIso8601ZuluString();
    }
}
