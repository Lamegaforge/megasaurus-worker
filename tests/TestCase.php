<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Traits\MockTwitchBearerTokenCache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUpTraits()
    {
        $traits = parent::setUpTraits();

        if (isset($traits[MockTwitchBearerTokenCache::class])) {
            $this->mockTwitchBearerTokenCache();
        }
    }
}
