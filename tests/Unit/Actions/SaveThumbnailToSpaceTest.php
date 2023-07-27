<?php

namespace Tests\Unit\Actions;

use Mockery;
use Tests\TestCase;
use App\Actions\SaveThumbnailToSpace;
use App\ValueObjects\Thumbnail;
use Illuminate\Support\Facades\Storage;
use App\Services\ContentFetcherService;
use App\Models\Clip;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaveThumbnailToSpaceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_save_thumbnail(): void
    {
        Storage::fake('digitalocean');

        $this->instance(
            ContentFetcherService::class,
            Mockery::mock(ContentFetcherService::class, function ($mock) {
                $mock->shouldReceive('fetch')->andReturn('...');
            })
        );

        $clip = Clip::factory()->create();

        $thumbnail = new Thumbnail(
            url: '',
        );

        app(SaveThumbnailToSpace::class)->handle($clip, $thumbnail);

        Storage::disk('digitalocean')->assertExists('thumbnails/' . $clip->uuid);
    }
}
