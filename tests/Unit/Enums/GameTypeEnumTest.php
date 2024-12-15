<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enums;

use App\Enums\GameTypeEnum;
use PHPUnit\Framework\TestCase;

class GameTypeEnumTest extends TestCase
{
    public function testValues(): void
    {
        $expectedValues = [
            'division_a',
            'division_b',
            'play_off',
            'semifinal',
            'final'
        ];

        // Assert that the values method returns the correct values for the enum
        $this->assertSame($expectedValues, GameTypeEnum::values());
    }

    public function testEnumCases(): void
    {
        // Check that the enum cases are correctly defined
        $this->assertSame(GameTypeEnum::DivisionA, GameTypeEnum::from('division_a'));
        $this->assertSame(GameTypeEnum::DivisionB, GameTypeEnum::from('division_b'));
        $this->assertSame(GameTypeEnum::PlayOff, GameTypeEnum::from('play_off'));
        $this->assertSame(GameTypeEnum::Semifinal, GameTypeEnum::from('semifinal'));
        $this->assertSame(GameTypeEnum::Final, GameTypeEnum::from('final'));
    }
}
