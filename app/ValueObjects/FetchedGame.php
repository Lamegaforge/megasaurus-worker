<?php

namespace App\ValueObjects;

readonly final class FetchedGame
{
    public function __construct(
        public string $external_id,
        public string $name,
        public Card $card,
    ) {}

    /** 
     * @param array{
     *  id: string,
     *  name: string,
     *  box_art_url: string,
     * } $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            external_id: $attributes['id'],
            name: $attributes['name'],
            card: Card::from([
                'id' => $attributes['id'],
                'box_art_url' => $attributes['box_art_url'],
            ]),
        );
    }
}
