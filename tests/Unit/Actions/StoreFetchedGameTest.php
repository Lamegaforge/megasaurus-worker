<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedGame;
use App\Models\Game;
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
        $fetchedGame = FetchedGame::from(
            TwitchStub::makeGame([
                'id' => 123456,
                'name' => 'game_name',
                'box_art_url' => 'https://box_art_url',
            ]),
        );

        $game = app(StoreFetchedGame::class)->handle($fetchedGame);

        $this->assertTrue($game->wasRecentlyCreated);

        Assert::assertArraySubset([
            'external_id' => '123456',
            'name' => 'game_name',
        ], $game->toArray());
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_game_already_saved(): void
    {
        $game = Game::factory()->create();

        $fetchedGame = FetchedGame::from(
            TwitchStub::makeGame([
                'id' => $game->external_id,
            ]),
        );

        $game = app(StoreFetchedGame::class)->handle($fetchedGame);

        $this->assertFalse($game->wasRecentlyCreated);
    }
}
