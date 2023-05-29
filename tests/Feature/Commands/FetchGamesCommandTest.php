<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use Domain\Models\Clip;
use Domain\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Stubs\TwitchStub;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\StoreFetchedGameJob;

class FetchGamesCommandTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function it_able_to_dispatch_games_in_jobs(): void
    {
        Queue::fake();

        $game = Game::factory()->create([
            'name' => null,
        ]);

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeGame(),
            ]], 200),
        ]);

        $this->artisan('app:fetch-games-command')->assertSuccessful();

        Http::assertSent(function ($request) use ($game) {
            return $request->url() === 'https://api.twitch.tv/helix/games?id=' . $game->external_id;
        });

        Queue::assertPushed(StoreFetchedGameJob::class);
    }

    /**
     * @test
     */
    public function it_able_to_dispatch_games_in_batch(): void
    {
        Queue::fake();

        Game::factory()->count(150)->create([
            'name' => null,
        ]);

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeGame(),
            ]], 200),
        ]);

        $this->artisan('app:fetch-games-command')->assertSuccessful();

        Http::assertSentCount(2);
        Queue::assertPushed(StoreFetchedGameJob::class, 2);
    }

    /**
     * @test
     */
    public function it_able_to_send_nothing_when_all_games_are_ready(): void
    {
        Queue::fake();

        Game::factory()->create();

        $this->artisan('app:fetch-games-command')->assertSuccessful();

        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }
}
