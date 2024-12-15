<?php

namespace App\DataFixtures;

use App\Entity\Team;
use App\Entity\User;
use App\Enums\DivisionEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user->setEmail("test@test.com");
        $user->setPassword('$2y$13$bMk/e9G4xmulHkOsHxDZ/.cnltHNavTcCEA/jXzoJv7yaMJ50s9Tu');
        $manager->persist($user);

        foreach (DivisionEnum::A->division() as $teamName) {
            $team = new Team();
            $team->id = Uuid::v4();
            $team->name = $teamName;
            $team->division = DivisionEnum::A;
            $manager->persist($team);
        }

        foreach (DivisionEnum::B->division() as $teamName) {
            $team = new Team();
            $team->id = Uuid::v4();
            $team->name = $teamName;
            $team->division = DivisionEnum::B;
            $manager->persist($team);
        }


        $manager->flush();
    }
}
