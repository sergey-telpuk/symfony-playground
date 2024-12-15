<?php

namespace App\Tests\Integration\Service;

use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\GameTypeEnum;
use App\Enums\TeamEnum;
use App\Service\GameService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

final class GameServiceTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;
    private GameService $game;

    public function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $this->em = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->game = self::getContainer()->get(GameService::class);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();

        $this->em->close();
        $this->em = null;
    }

    public function testPlayAllGames(): void
    {
        $this->game->playAllGames();

        $gamesDivisionA = $this->em->createQuery(
            'SELECT g FROM App\Entity\Game g WHERE g.gameType = :game_type',
        )->setParameter('game_type', GameTypeEnum::DivisionA)->getResult();

        $this->assertCount(56, $gamesDivisionA);

        $gamesDivisionB = $this->em->createQuery(
            'SELECT g FROM App\Entity\Game g WHERE g.gameType = :game_type',
        )->setParameter('game_type', GameTypeEnum::DivisionB)->getResult();

        $this->assertCount(56, $gamesDivisionB);

        $gamesPlayoff = $this->em->createQuery(
            'SELECT g FROM App\Entity\Game g WHERE g.gameType = :game_type',
        )->setParameter('game_type', GameTypeEnum::PlayOff)->getResult();

        $this->assertCount(4, $gamesPlayoff);

        $gamesSemifinal = $this->em->createQuery(
            'SELECT g FROM App\Entity\Game g WHERE g.gameType = :game_type',
        )->setParameter('game_type', GameTypeEnum::Semifinal)->getResult();

        $this->assertCount(2, $gamesSemifinal);

        $gamesFinal = $this->em->createQuery(
            'SELECT g FROM App\Entity\Game g WHERE g.gameType = :game_type',
        )->setParameter('game_type', GameTypeEnum::Final)->getResult();

        $this->assertCount(1, $gamesFinal);
    }

    public function testPlay(): void
    {
        foreach (self::usecases() as ['team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $winner]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->flush();

            $this->game->play(gameId: $gameId = Uuid::v4(), teamOne: $teamOne, teamTwo: $teamTwo, gameType: GameTypeEnum::Final);

            $game = $this->em->createQuery(
                'SELECT g FROM App\Entity\Game g WHERE g.id = :id',
            )->setParameter('id', $gameId)->getOneOrNullResult();

            $this->assertTrue($game?->teamTwo->equal($winner));
        }
    }

    public function testResetAllGames(): void
    {
        foreach (self::usecases() as ['team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $winner]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->flush();

            $this->game->resetAllGames();

            $games = $this->em->createQuery(
                'SELECT g FROM App\Entity\Game g',
            )->getResult();

            $this->assertEmpty($games);
        }
    }

    static private function usecases(): iterable
    {
        [$teamOne, $teamTwo] = [new Team(), new Team()];
        $teamOne->id = Uuid::v4();
        $teamOne->name = TeamEnum::A;
        $teamOne->division = DivisionEnum::A;
        $teamTwo->id = Uuid::v4();
        $teamTwo->name = TeamEnum::B;
        $teamTwo->division = DivisionEnum::A;

        yield ['team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $teamTwo];
        yield ['team_one' => $teamTwo, 'team_two' => $teamOne, 'winner' => $teamOne];
    }
}
