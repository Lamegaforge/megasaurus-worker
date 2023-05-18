<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\FetchGamesFromExternalIds;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedGame;
use Tests\Stubs\TwitchStub;
use Illuminate\Support\Collection;
use Tests\Traits\MockTwitchBearerTokenCache;

class FetchGamesFromExternalIdsTest extends TestCase
{
    use RefreshDatabase;
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function its_able_to_find_a_list_of_fetch_clips(): void
    {
        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeGame(),
                TwitchStub::makeGame(),
                TwitchStub::makeGame(),
            ]], 200),
        ]);

        $fetchedGames = app(FetchGamesFromExternalIds::class)->handle(
            externalIdList: collect(1, 2, 3),
        );

        $this->assertInstanceOf(Collection::class, $fetchedGames);
        $this->assertCount(3, $fetchedGames);

        $fetchedGame = $fetchedGames->first();

        $this->assertInstanceOf(FetchedGame::class, $fetchedGame);
    }
}
