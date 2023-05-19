<?php

namespace App\ValueObjects;

use App\Enums\CardEnum;
use App\Services\ContentFetcherService;

readonly final class Card
{
    public function __construct(
        public string $name,
        public string $url,
    ) {}

    public static function from(array $attributes): self
    {
        $url = $attributes['box_art_url'];

        $url = str_replace(['{width}', '{height}'], [
            CardEnum::Width->value,
            CardEnum::Height->value,
        ], $url);

        return new self(
            name: $attributes['id'],
            url: $url,
        );
    }

    public function content(): string
    {
        return app(ContentFetcherService::class)->fetch($this->url);
    }
}
