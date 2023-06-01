<?php

namespace Tests\Unit\Actions;

use Mockery;
use Tests\TestCase;
use App\Actions\SaveCardToSpace;
use App\ValueObjects\Card;
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
            'id' => '165484',
            'box_art_url' => '',
        ]);

        app(SaveCardToSpace::class)->handle($card);

        Storage::disk('digitalocean')->assertExists('cards/165484');
    }
}
