<?php

namespace App\ValueObjects;

readonly final class BearerToken
{
    public function __construct(
        public string $value,
    ) {}
}
