<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\FetchClipsFromExternalIds;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\FetchedClip;
use Tests\Stubs\TwitchStub;
use Illuminate\Support\Collection;
use Tests\Traits\MockTwitchBearerTokenCache;

class FetchClipsFromExternalIdsTest extends TestCase
{
    use MockTwitchBearerTokenCache;

    /**
     * @test
     */
    public function it_able_to_fetch_clips():void
    {
        Http::fake([
            'api.twitch.tv/*' => Http::response(['data' => [
                TwitchStub::makeClip(),
                TwitchStub::makeClip(),
                TwitchStub::makeClip(),
            ]], 200),
        ]);

        $fetchedClips = app(FetchClipsFromExternalIds::class)->handle(
            externalIdList: collect(1, 2, 3),
        );

        $this->assertInstanceOf(Collection::class, $fetchedClips);
        $this->assertCount(3, $fetchedClips);

        $fetchedClip = $fetchedClips->first();

        $this->assertInstanceOf(FetchedClip::class, $fetchedClip);
    }
}
