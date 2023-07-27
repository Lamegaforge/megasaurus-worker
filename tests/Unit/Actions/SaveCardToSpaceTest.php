<?php

namespace Tests\Unit\Actions;

use Mockery;
use Tests\TestCase;
use App\Actions\SaveCardToSpace;
use App\ValueObjects\Card;
use Illuminate\Support\Facades\Storage;
use App\Services\ContentFetcherService;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaveCardToSpaceTest extends TestCase
{
    use RefreshDatabase;

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

        $game = Game::Factory()->create();

        $card = Card::from([
            'box_art_url' => '',
        ]);

        app(SaveCardToSpace::class)->handle($game, $card);

        Storage::disk('digitalocean')->assertExists('cards/' . $game->uuid);
    }
}
