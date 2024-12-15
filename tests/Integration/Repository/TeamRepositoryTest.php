<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\TeamEnum;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class TeamRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;

    private TeamRepository $repo;

    protected function setUp(): void
    {
        self::bootKernel([
            'environment' => 'test',
            'debug' => false,
        ]);

        $this->em = self::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repo = self::getContainer()->get(TeamRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        self::ensureKernelShutdown();
        $this->em->close();
        $this->em = null;
    }

    public function testFindByDivision(): void
    {
        $divisionA = $this->repo->findByDivision(division: DivisionEnum::A);
        $this->assertIsArray($divisionA);
        $this->assertNotEmpty($divisionA);
        $this->assertEquals(DivisionEnum::A, $divisionA[0]->division);

        $divisionB = $this->repo->findByDivision(division: DivisionEnum::B);
        $this->assertIsArray($divisionB);
        $this->assertNotEmpty($divisionB);
        $this->assertEquals(DivisionEnum::B, $divisionB[0]->division);
    }

    public function testPersistingAndRetrievingTeam(): void
    {
        $uuid = Uuid::v4();

        $team = new Team();
        $team->id = $uuid;
        $team->name = TeamEnum::A;
        $team->division = DivisionEnum::A;

        $this->em->persist($team);
        $this->em->flush();

        $persistedTeam = $this->em->getRepository(Team::class)->find($uuid);
        $this->assertNotNull($persistedTeam);
        $this->assertEquals($uuid, $persistedTeam->id);
        $this->assertEquals(TeamEnum::A, $persistedTeam->name);
        $this->assertEquals(DivisionEnum::A, $persistedTeam->division);
    }
}
