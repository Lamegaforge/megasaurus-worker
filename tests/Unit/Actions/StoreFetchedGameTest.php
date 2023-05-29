<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedGame;
use Domain\Models\Game;
use App\Actions\StoreFetchedGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\TwitchStub;
use Illuminate\Testing\Assert;

class StoreFetchedGameTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_game(): void
    {
        $game = Game::factory()->create([
            'name' => null,
        ]);

        $fetchedGame = FetchedGame::from(
            TwitchStub::makeGame([
                'id' => $game->external_id,
                'name' => 'game_name',
                'box_art_url' => 'https://box_art_url',
            ]),
        );

        app(StoreFetchedGame::class)->handle($fetchedGame);

        $game->refresh();

        $this->assertSame('game_name', $game->name);
    }
}
