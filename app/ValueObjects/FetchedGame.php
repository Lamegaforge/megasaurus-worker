<?php

namespace App\ValueObjects;

readonly final class FetchedGame
{
    public function __construct(
        public string $external_id,
        public string $name,
        public Card $card,
    ) {}

    public static function from(array $attributes): self
    {
        return new self(
            external_id: $attributes['id'],
            name: $attributes['name'],
            card: Card::from($attributes),
        );
    }
}
