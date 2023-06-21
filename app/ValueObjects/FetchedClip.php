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

    /** 
     * @param array{
     *  id: string,
     *  game_id: string,
     *  title: string,
     *  creator_id: string,
     *  creator_name: string,
     *  url: string,
     *  thumbnail_url: string,
     *  view_count: int,
     *  duration: int,
     *  created_at: string,
     * } $attributes
     */
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
                url: $attributes['thumbnail_url'],
            ),
            views: $attributes['view_count'],
            duration: $attributes['duration'],
            published_at: self::applyTimezoneOnCreatedAt($attributes),
        );
    }

    /** 
     * Twitch default timezone is UTC
     * 
     * @see https://discuss.dev.twitch.tv/t/twitch-api-timezone/11322
     */
    private static function applyTimezoneOnCreatedAt(array $attributes): Carbon
    {
        $publishedAt = Carbon::parse($attributes['created_at'], 'UTC');

        $publishedAt->setTimezone('Europe/Paris');

        return $publishedAt;
    }
}
