<?php

namespace Tests\Unit\Actions;

use Mockery;
use Tests\TestCase;
use App\Actions\SaveThumbnailToSpace;
use App\ValueObjects\Thumbnail;
use Illuminate\Support\Facades\Storage;
use App\Services\ContentFetcherService;

class SaveThumbnailToSpaceTest extends TestCase
{
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

        $thumbnail = new Thumbnail(
            name: 'thumbnail_name',
            url: '',
        );

        app(SaveThumbnailToSpace::class)->handle($thumbnail);

        Storage::disk('digitalocean')->assertExists('thumbnails/thumbnail_name');
    }
}
