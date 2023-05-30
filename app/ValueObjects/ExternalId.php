<?php

namespace App\ValueObjects;

use Stringable;

readonly final class ExternalId implements Stringable
{
    public function __construct(
        public string $value,
    ) {}

    public function __toString(): string
    {
        return $this->value;
    }
}
