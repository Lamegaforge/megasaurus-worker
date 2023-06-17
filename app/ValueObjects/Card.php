<?php

namespace App\ValueObjects;

use App\Enums\CardEnum;
use App\Services\ContentFetcherService;

readonly final class Card
{
    public function __construct(
        public string $url,
    ) {}

    /** 
     * @param array{
     *  box_art_url: string,
     * } $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            url: self::makeUrl($attributes),
        );
    }

    public function content(): string
    {
        return app(ContentFetcherService::class)->fetch($this->url);
    }

    /** 
     * @param array{
     *  box_art_url: string,
     * } $attributes
     */
    private static function makeUrl(array $attributes): string
    {
        $url = $attributes['box_art_url'];

        $url = str_replace(['{width}', '{height}'], [
            CardEnum::Width->value,
            CardEnum::Height->value,
        ], $url);

        return $url;
    }
}
