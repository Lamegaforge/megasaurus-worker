<?php

namespace App\ValueObjects;

readonly final class Card
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}

    public static function from(array $attributes): self
    {
        $url = $attributes['box_art_url'];

        $url = str_replace('{width}', 384, $url);
        $url = str_replace('{height}', 576, $url);

        return new self(
            name: $attributes['id'],
            url: $url,
        );
    }

    public function content()
    {
        return file_get_contents($this->url);
    }
}
