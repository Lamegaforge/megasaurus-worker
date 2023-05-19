<?php

namespace Tests\Stubs;

class TwitchStub
{
    public static function makeClip(array $attributes = []): array
    {
        return array_merge([
            'id' => 'SavageMoldyKoalaKappaClaus',
            'game_id' => '509658',
            'title' => 'Jaime le futbole',
            'creator_id' => '519157370',
            'creator_name' => 'Dig_Bill',
            'url' => 'https://clips.twitch.tv/SavageMoldyKoalaKappaClaus',
            'thumbnail_url' => 'https://clips-media-assets2.twitch.tv/AT-cm%7C933243563-preview-480x272.jpg',
            'view_count' => '157',
            'duration' => '25',
            'created_at' => '2020-11-15T20:58:38Z',
        ], $attributes);
    }

    public static function makeGame(array $attributes = []): array
    {
        return array_merge([
            'id' => 1,
            'name' => 1,
            'box_art_url' => 1,
        ], $attributes);
    }
}
