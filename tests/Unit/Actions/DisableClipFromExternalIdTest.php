<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use Domain\Enums\ClipStateEnum;
use App\Models\Clip;
use App\Actions\DisableClipFromExternalId;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ValueObjects\ExternalId;

class DisableClipFromExternalIdTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_disable_a_clip(): void
    {
        $clip = Clip::factory()->create();

        app(DisableClipFromExternalId::class)->handle(
            new ExternalId($clip->external_id),
        );

        $clip->refresh();

        $this->assertEquals(ClipStateEnum::Disable, $clip->state);
    }
}
