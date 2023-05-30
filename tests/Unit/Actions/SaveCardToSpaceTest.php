<?php

namespace Tests\Unit\Actions;

use Mockery;
use Tests\TestCase;
use App\Actions\SaveCardToSpace;
use App\ValueObjects\Card;
use App\ValueObjects\ExternalId;
use Illuminate\Support\Facades\Storage;
use App\Services\ContentFetcherService;

class SaveCardToSpaceTest extends TestCase
{
    /**
     * @test
     */
    public function it_able_to_save_card(): void
    {
        Storage::fake('digitalocean');

        $this->instance(
            ContentFetcherService::class,
            Mockery::mock(ContentFetcherService::class, function ($mock) {
                $mock->shouldReceive('fetch')->andReturn('...');
            })
        );

        $card = Card::from([
            'box_art_url' => '',
        ]);

        $externalId = new ExternalId(1);

        app(SaveCardToSpace::class)->handle($externalId, $card);

        Storage::disk('digitalocean')->assertExists('cards/' . $externalId);
    }
}
