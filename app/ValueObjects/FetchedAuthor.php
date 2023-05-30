<?php

namespace App\ValueObjects;

readonly final class FetchedAuthor
{
    public function __construct(
        public ExternalId $externalId,
        public string $name,
    ) {}
}
