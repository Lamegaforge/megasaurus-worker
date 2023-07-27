<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\FetchGameFromExternalId;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedGame;
use App\ValueObjects\ExternalId;
use Tests\Stubs\TwitchStub;
use Tests\Traits\MockTwitchBearerTokenCache;

class FetchGamesFromExternalIdTest extends TestCase
{
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function it_able_to_fetch_game(): void
    {
        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeGame(),
            ]], 200),
        ]);

        $fetchedGame = app(FetchGameFromExternalId::class)->handle(
            new ExternalId(1),
        );

        $this->assertInstanceOf(FetchedGame::class, $fetchedGame);
    }
}
