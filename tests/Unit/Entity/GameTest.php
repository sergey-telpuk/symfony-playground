<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\TeamEnum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class GameTest extends TestCase
{
    public function testEqual(): void
    {
        $uuid = Uuid::v4();

        $team1 = new Team();
        $team1->id = $uuid;
        $team1->name = TeamEnum::A;
        $team1->division = DivisionEnum::A;

        $team2 = new Team();
        $team2->id = $uuid;
        $team2->name = TeamEnum::A;
        $team2->division = DivisionEnum::A;

        $this->assertTrue($team1->equal($team2));
    }

    public function testNotEqual(): void
    {
        $team1 = new Team();
        $team1->id = Uuid::v4();
        $team1->name = TeamEnum::A;
        $team1->division = DivisionEnum::A;

        $team2 = new Team();
        $team2->id = Uuid::v4();
        $team2->name = TeamEnum::A;
        $team2->division = DivisionEnum::A;

        $this->assertFalse($team1->equal($team2));
    }
}
