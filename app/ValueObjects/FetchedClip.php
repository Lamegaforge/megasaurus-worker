<?php

namespace App\ValueObjects;

use Carbon\Carbon;

readonly final class FetchedClip
{
    public function __construct(
        public ExternalId $externalId,
        public ?ExternalId $externalGameId,
        public string $title,
        public FetchedAuthor $author,
        public string $url,
        public Thumbnail $thumbnail,
        public int $views,
        public int $duration,
        public Carbon $published_at,
    ) {}

    public static function from(array $attributes): self
    {
        return new self(
            externalId: new ExternalId($attributes['id']),
            externalGameId: $attributes['game_id'] ? new ExternalId($attributes['game_id']) : null,
            title: $attributes['title'],
            author: new FetchedAuthor(
                externalId: new ExternalId($attributes['creator_id']),
                name: $attributes['creator_name'],
            ),
            url: $attributes['url'],
            thumbnail: new Thumbnail(
                name: $attributes['id'],
                url: $attributes['thumbnail_url'],
            ),
            views: $attributes['view_count'],
            duration: $attributes['duration'],
            published_at: Carbon::parse($attributes['created_at']),
        );
    }
}
