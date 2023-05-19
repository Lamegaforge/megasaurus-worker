<?php

namespace Tests\Unit\ValueObjects;

use Tests\TestCase;
use App\ValueObjects\Card;

class CardTest extends TestCase
{
    /**
     * @test
     */
    public function it_able_to_instantiate(): void
    {
        $card = Card::from([
            'id' => 'Crusader Kings III',
            'box_art_url' => 'https://static-cdn.jtvnw.net/ttv-boxart/514888_IGDB-{width}x{height}.jpg',
        ]);

        $this->assertInstanceOf(Card::class, $card);
        $this->assertEquals('Crusader Kings III', $card->name);
        $this->assertEquals(
            'https://static-cdn.jtvnw.net/ttv-boxart/514888_IGDB-384x576.jpg', 
            $card->url,
        );
    }
}
