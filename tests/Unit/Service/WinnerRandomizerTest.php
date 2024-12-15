<?php

namespace App\Tests\Unit\Service;

use App\Entity\Team;
use App\Service\DigitalRandomizerInterface;
use App\Service\WinnerRandomizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

final class WinnerRandomizerTest extends TestCase
{
    public function testDefineWinner(): void
    {
        foreach (self::usecases() as ['winner_randomizer' => $case, 'teams' => [$teamOne, $teamTwo], 'winner' => $winner]) {
            $def = $case->defineWinner($teamOne, $teamTwo);
            $this->assertEquals($winner, $def);
        }
    }

    static private function usecases(): iterable
    {
        [$teamOne, $teamTwo] = [new Team(), new Team()];
        $teamOne->id = Uuid::v4();
        $teamTwo->id = Uuid::v4();

        yield ['winner_randomizer' => new WinnerRandomizer(
            new class() implements DigitalRandomizerInterface {
                public function digital1or0(): int
                {
                    return 1;
                }
            }
        ), 'teams' => [$teamOne, $teamTwo], 'winner' => $teamTwo];

        yield ['winner_randomizer' => new WinnerRandomizer(
            new class() implements DigitalRandomizerInterface {
                public function digital1or0(): int
                {
                    return 0;
                }
            }
        ), 'teams' => [$teamOne, $teamTwo], 'winner' => $teamOne];
    }
}
