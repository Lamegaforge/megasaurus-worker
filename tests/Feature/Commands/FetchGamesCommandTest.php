<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Clip;
use App\Models\Game;
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

        $clip = Clip::factory()->create();

        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeGame(),
            ]], 200),
        ]);

        $this->artisan('app:fetch-games-command')->assertSuccessful();

        Http::assertSent(function ($request) use ($clip) {
            return $request->url() === 'https://api.twitch.tv/helix/games?id=' . $clip->external_game_id;
        });

        Queue::assertPushed(StoreFetchedGameJob::class);
    }

    /**
     * @test
     */
    public function it_able_to_send_nothing_when_all_clips_have_a_game(): void
    {
        Queue::fake();

        Clip::factory()
            ->for(Game::factory())
            ->create();

        $this->artisan('app:fetch-games-command')->assertSuccessful();

        Http::assertNothingSent();
        Queue::assertNothingPushed();
    }
}
