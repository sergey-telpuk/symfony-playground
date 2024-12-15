<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Game;
use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\GameTypeEnum;
use App\Enums\TeamEnum;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class GameRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;

    private GameRepository $repo;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $this->em = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repo = self::getContainer()->get(GameRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
        $this->em->close();
        $this->em = null;
    }

    public function testFindWinnerScore(): void
    {
        /**
         * @var $game Game
         * @var $teamOne Team
         * @var $teamTwo Team
         */
        foreach (self::games() as ['game' => $game, 'team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $winner]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->persist($game);
            $this->em->flush();

            /**
             * @var $winners array<array{score: int, team: Team}>
             */
            $winners = $this->repo->findWinnerScore(gameType: $game->gameType, reverse: false, count: 100);
            $this->assertIsArray($winners);
            $this->assertNotEmpty($winners);

            foreach ($winners as ['score' => $score, 'team' => $gotWinner]) {
                $this->assertGreaterThan(0, $score);
                $this->assertInstanceOf(Team::class, $winner);
                $this->assertTrue($winner->equal($gotWinner));
            }
        }
    }

    public function testFindScoreByTeamOne(): void
    {
        /**
         * @var $game Game
         * @var $teamOne Team
         * @var $teamTwo Team
         */
        foreach (self::games() as ['game' => $game, 'team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $winner]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->persist($game);
            $this->em->flush();

            /**
             * @var $winner array<array{score: int, team: Team}>
             */
            $winners = $this->repo->findScoreByTeamOne(gameType: $game->gameType);
            $this->assertIsArray($winners);
            $this->assertNotEmpty($winners);

            foreach ($winners as ['score' => $score, 'team' => $gotWinner]) {
                $this->assertInstanceOf(Team::class, $winner);
                $this->assertFalse($winner->equal($gotWinner));
            }
        }
    }

    public function testFindByGameType(): void
    {
        /**
         * @var $game Game
         * @var $teamOne Team
         * @var $teamTwo Team
         */
        foreach (self::games() as ['game' => $game, 'team_one' => $teamOne, 'team_two' => $teamTwo]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->persist($game);
            $this->em->flush();

            /**
             * @var $got Game
             */
            $got = $this->repo->findByGameType(gameType: $game->gameType);
            $this->assertIsArray($got);
            $this->assertNotEmpty($got);
            $this->assertEquals($got[0]->gameType, $game->gameType);
        }
    }

    static private function games(): iterable
    {
        foreach (GameTypeEnum::cases() as $gameType) {
            $teamOne = new Team();
            $teamOne->id = Uuid::v4();
            $teamOne->name = TeamEnum::A;
            $teamOne->division = DivisionEnum::A;

            $teamTwo = new Team();
            $teamTwo->id = Uuid::v4();
            $teamTwo->name = TeamEnum::A;
            $teamTwo->division = DivisionEnum::A;

            $game = new Game();
            $game->id = Uuid::v4();
            $game->teamOne = $teamOne;
            $game->teamTwo = $teamTwo;
            $game->winner = $teamTwo;
            $game->teamTwoScore = 1;
            $game->playedAt = new \DateTimeImmutable();
            $game->gameType = $gameType;
            yield ['game' => $game, 'team_one' => $game->teamOne, 'team_two' => $game->teamTwo, 'winner' => $game->winner];
        }
    }
}
