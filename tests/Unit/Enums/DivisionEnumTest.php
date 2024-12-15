<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enums;

use App\Enums\DivisionEnum;
use App\Enums\TeamEnum;
use PHPUnit\Framework\TestCase;

class DivisionEnumTest extends TestCase
{
    public function testValues(): void
    {
        $expectedValues = ['A', 'B'];
        $this->assertSame($expectedValues, DivisionEnum::values());
    }

    public function testDivisionA(): void
    {
        $divisionA = DivisionEnum::A;

        // Expected teams for Division A
        $expectedTeams = [
            TeamEnum::A, TeamEnum::B, TeamEnum::C, TeamEnum::D,
            TeamEnum::E, TeamEnum::F, TeamEnum::G, TeamEnum::H
        ];

        $this->assertSame($expectedTeams, $divisionA->division());
    }

    public function testDivisionB(): void
    {
        $divisionB = DivisionEnum::B;

        // Expected teams for Division B
        $expectedTeams = [
            TeamEnum::I, TeamEnum::J, TeamEnum::K, TeamEnum::L,
            TeamEnum::M, TeamEnum::N, TeamEnum::O, TeamEnum::P
        ];

        $this->assertSame($expectedTeams, $divisionB->division());
    }
}
