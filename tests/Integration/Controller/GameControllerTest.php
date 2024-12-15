<?php

namespace App\Tests\Integration\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testPlay(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/games/play');

        $this->assertResponseIsSuccessful();
    }
}
