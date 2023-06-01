<?php

namespace App\ValueObjects;

use App\Enums\CardEnum;
use App\Services\ContentFetcherService;

readonly final class Card
{
    public function __construct(
        public string $id,
        public string $url,
    ) {}

    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            url: self::makeUrl($attributes),
        );
    }

    public function content(): string
    {
        return app(ContentFetcherService::class)->fetch($this->url);
    }

    private static function makeUrl($attributes): string
    {
        $url = $attributes['box_art_url'];

        $url = str_replace(['{width}', '{height}'], [
            CardEnum::Width->value,
            CardEnum::Height->value,
        ], $url);

        return $url;
    }
}
