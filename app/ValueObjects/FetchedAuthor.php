<?php

namespace App\ValueObjects;

readonly final class FetchedAuthor
{
    public function __construct(
        public string $external_id,
        public string $name,
    ) {}
}
