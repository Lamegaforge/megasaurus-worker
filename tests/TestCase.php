<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\MockTwitchBearerTokenCache;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    protected function setUpTraits()
    {
        $traits = parent::setUpTraits();

        if (isset($traits[MockTwitchBearerTokenCache::class])) {
            $this->mockTwitchBearerTokenCache();
        }
    }
}
