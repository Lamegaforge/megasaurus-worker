<?php

namespace Tests\Stubs;

class TwitchStub
{
    public static function makeClip(array $attributes = []): array
    {
        return array_merge([
            'id' => 1,
            'game_id' => 1,
            'title' => 1,
            'creator_id' => 1,
            'creator_name' => 1,
            'url' => 1,
            'thumbnail_url' => 1,
            'view_count' => 1,
            'duration' => 1,
            'created_at' => 1,
        ], $attributes);
    }
}
