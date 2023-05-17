<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SuspiciousClipDetector;
use App\ValueObjects\FetchedClip;
use App\Enums\ClipStateEnum;

class SuspiciousClipDetectorTest extends TestCase
{
    /**
     * @test
     */
    public function it_able_to_determine_that_clip_is_ok(): void
    {
        $fetchedClip = $this->makeFetchedClip();

        $state = app(SuspiciousClipDetector::class)->fromFetchedClip($fetchedClip);

        $this->assertTrue($state === ClipStateEnum::Ok);
    }

    /**
     * @test
     */
    public function it_able_to_detect_suspicious_clip_because_of_its_duration(): void
    {
        $fetchedClip = $this->makeFetchedClip([
            'duration' => 30,
        ]);

        $state = app(SuspiciousClipDetector::class)->fromFetchedClip($fetchedClip);

        $this->assertTrue($state === ClipStateEnum::Suspicious);
    }

    /**
     * @test
     * @dataProvider suspiciousTitleProvider
     */
    public function it_able_to_detect_suspicious_clip_due_to_its_title(string $title): void
    {
        $fetchedClip = $this->makeFetchedClip([
            'title' => $title,
        ]);

        $state = app(SuspiciousClipDetector::class)->fromFetchedClip($fetchedClip);

        $this->assertTrue($state === ClipStateEnum::Suspicious);
    }

    public static function suspiciousTitleProvider(): array
    {
        return [
            ['Windjammers France • 9th anniversary｢streamer: Adwim｣'],
            ['Windjammers Winter 2021 : Round #2🥏 ｢streamer: LMF｣'],
            ['Une bonne ratatouille de démos ｢streamer: Spider & Russian｣'],
            ["UN P 'TIT COUP DANS LES BAGUETTES ｢streamer: Ruru｣"],
            ["S.W.A.T : Special Weapons and Tartoches ｢streamer: LMF｣"],
            ["Le film où Sean connery il est moine mais dans RE4, j'sais pas ｢streamer: Spidaire｣"],
            ["𝕷'𝕳𝖔𝖗𝖗𝖎𝖇𝖑𝖊 𝕺𝖈𝖙𝖔𝖇𝖗𝖊 - Chapitre VIII : CHACUN SA MERDE 👻 (+ Phasmophobia!) ｢streamer: LMF｣ "],
        ];
    }

    private function makeFetchedClip(array $attributes = []): FetchedClip
    {
        $attributes = array_merge([
            'id' => '',
            'game_id' => '',
            'title' => '',
            'creator_id' => '',
            'creator_name' => '',
            'url' => '',
            'thumbnail_url' => '',
            'view_count' => 0,
            'duration' => 0,
            'created_at' => '',
        ], $attributes);

        return FetchedClip::from($attributes);
    }
}
