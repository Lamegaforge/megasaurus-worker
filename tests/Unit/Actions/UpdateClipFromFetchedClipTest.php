<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedClip;
use App\Models\Clip;
use App\Actions\UpdateClipFromFetchedClip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Stubs\TwitchStub;

class UpdateClipFromFetchedClipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_game(): void
    {
        $clip = Clip::factory()->create();

        $fetchedClip = FetchedClip::from(
            TwitchStub::makeClip([
                'id' => $clip->external_id,
                'title' => 'updated_title',
                'view_count' => 750,
            ]),
        );

        app(UpdateClipFromFetchedClip::class)->handle($fetchedClip);

        $this->assertDatabaseHas('clips', [
            'external_id' => $clip->external_id,
            'title' => 'updated_title',
            'views' => 750,
        ]);
    }
}
