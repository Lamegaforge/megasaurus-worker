<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\ValueObjects\FetchedAuthor;
use App\ValueObjects\ExternalId;
use Domain\Models\Author;
use App\Actions\StoreAuthorFromFetchedAuthor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreAuthorFromFetchedAuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_able_to_store_fetched_author(): void
    {
        $fetchedAuthor = new FetchedAuthor(
            externalId: new ExternalId('1'),
            name: 'Biil',
        );

        $author = app(StoreAuthorFromFetchedAuthor::class)->handle($fetchedAuthor);

        $this->assertTrue($author->wasRecentlyCreated);

        $this->assertSame('1', $author->external_id);
        $this->assertSame('Biil', $author->name);
    }

    /**
     * @test
     */
    public function it_able_to_store_fetched_author_already_saved(): void
    {
        $author = Author::factory()->create();

        $fetchedAuthor = new FetchedAuthor(
            externalId: new ExternalId($author->external_id),
            name: 'Biil',
        );

        $storedAuthor = app(StoreAuthorFromFetchedAuthor::class)->handle($fetchedAuthor);

        $this->assertFalse($storedAuthor->wasRecentlyCreated);
        $this->assertTrue($storedAuthor->is($author));
    }
}
