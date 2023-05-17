<?php

namespace App\ValueObjects;

use Carbon\Carbon;

readonly final class FetchedClip
{
    public function __construct(
        public string $external_id,
        public string $external_game_id,
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
            external_id: $attributes['id'],
            external_game_id: $attributes['game_id'],
            title: $attributes['title'],
            author: new FetchedAuthor(
                external_id: $attributes['creator_id'],
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
