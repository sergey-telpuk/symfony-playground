<?php

declare(strict_types=1);

namespace App\Tests\Unit\Enums;

use App\Enums\TeamEnum;
use PHPUnit\Framework\TestCase;

class TeamEnumTest extends TestCase
{
    public function testValues(): void
    {
        $expectedValues = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'
        ];

        // Assert that the values method returns all team values in the correct order
        $this->assertSame($expectedValues, TeamEnum::values());
    }

    public function testEnumCases(): void
    {
        // Assert that each string value corresponds to the correct enum case
        $this->assertSame(TeamEnum::A, TeamEnum::from('A'));
        $this->assertSame(TeamEnum::B, TeamEnum::from('B'));
        $this->assertSame(TeamEnum::C, TeamEnum::from('C'));
        $this->assertSame(TeamEnum::D, TeamEnum::from('D'));
        $this->assertSame(TeamEnum::E, TeamEnum::from('E'));
        $this->assertSame(TeamEnum::F, TeamEnum::from('F'));
        $this->assertSame(TeamEnum::G, TeamEnum::from('G'));
        $this->assertSame(TeamEnum::H, TeamEnum::from('H'));
        $this->assertSame(TeamEnum::I, TeamEnum::from('I'));
        $this->assertSame(TeamEnum::J, TeamEnum::from('J'));
        $this->assertSame(TeamEnum::K, TeamEnum::from('K'));
        $this->assertSame(TeamEnum::L, TeamEnum::from('L'));
        $this->assertSame(TeamEnum::M, TeamEnum::from('M'));
        $this->assertSame(TeamEnum::N, TeamEnum::from('N'));
        $this->assertSame(TeamEnum::O, TeamEnum::from('O'));
        $this->assertSame(TeamEnum::P, TeamEnum::from('P'));
    }
}
