<?php

namespace App\Tests\Integration\Service;

use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\GameTypeEnum;
use App\Enums\TeamEnum;
use App\Service\DigitalRandomizer;
use App\Service\DigitalRandomizerInterface;
use App\Service\GameInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Uid\Uuid;

final class GameServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private GameInterface $game;

    public function setUp(): void
    {
        $kernel = self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $application = new Application($kernel);
        $command = $application->find('doctrine:migrations:migrate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['n']);

        $this->em = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->game = self::getContainer()->get(GameInterface::class);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
    }

    public function testPlay(): void
    {
        $mockedService = $this->createMock(DigitalRandomizerInterface::class);
        self::getContainer()->set(DigitalRandomizer::class, $mockedService);
        $mockedService->method('digital1or0')->willReturn(1);

        foreach (self::usecases() as ['team_one' => $teamOne, 'team_two' => $teamTwo, 'winner' => $winner]) {
            $this->em->persist($teamOne);
            $this->em->persist($teamTwo);
            $this->em->flush();

            $game = $this->game->play(teamOne: $teamOne, teamTwo: $teamTwo, gameType: GameTypeEnum::Final);

            $game = $this->em->createQuery(
                'SELECT g FROM App\Entity\Game g WHERE g.id = :id',
            )->setParameter('id', $game->id)->getOneOrNullResult();

            $this->assertTrue($game?->teamTwo->equal($winner));
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
