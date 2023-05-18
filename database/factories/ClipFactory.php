<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ClipStateEnum;
use App\Models\Clip;
use App\Models\Author;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Clip>
 */
class ClipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => fake()->randomNumber(8, true),
            'external_game_id' => fake()->randomNumber(8, true),
            'url' => 'https://clips.twitch.tv/NaiveGoodWoodcockNinjaGrumpy-25Uu8L5urSxWo66M',
            'title' => fake()->sentence(),
            'views' => fake()->randomNumber(3),
            'duration' => 15,
            'state' => ClipStateEnum::Ok,
            'published_at' => '2023-01-01',
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Clip $clip) {
            if ($clip->author()->doesntExist()) {
                $clip->author()->associate(Author::factory()->create());
            }
        });
    }
}
