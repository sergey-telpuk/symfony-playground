<?php

namespace App\Tests\Integration\Controller;

use App\Entity\Game;
use App\Enums\GameTypeEnum;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testPlay(): void
    {
        $client = static::createClient();

        $client->followRedirects();

        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/games/play');

        $this->assertResponseIsSuccessful();
    }

    public function testGames(): void
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        $client->request('GET', '/games/play');

        $client->request('GET', '/games');

        $this->assertResponseStatusCodeSame(200);

        /**
         * @var $final array<Game>
         */
        $final = static::getContainer()->get(GameRepository::class)->findByGameType(GameTypeEnum::Final);

        $this->assertSelectorTextContains(
            '.winner',
            $final[0]?->winner->name->value . ' ' . '(' . $final[0]?->winner->division->value . ')');
    }


}
