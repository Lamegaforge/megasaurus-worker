<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\GetTwitchBearerToken;
use Illuminate\Support\Facades\Http;
use App\ValueObjects\BearerToken;

class GetTwitchBearerTokenTest extends TestCase
{
    /**
     * @test
     */
    public function it_able_to_get_token(): void
    {
        Http::fake([
            'id.twitch.tv/oauth2/token' => Http::response([
                'access_token' => 'access_token_value'
            ], 200),
        ]);

        $bearerToken = app(GetTwitchBearerToken::class)->handle();

        $this->assertInstanceOf(BearerToken::class, $bearerToken);
        $this->assertEquals('access_token_value', $bearerToken->value);
    }
}
