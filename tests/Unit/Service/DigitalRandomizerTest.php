<?php

namespace App\Tests\Unit\Service;

use App\Service\DigitalRandomizer;
use PHPUnit\Framework\TestCase;

final class DigitalRandomizerTest extends TestCase
{
    public function testDigital1or0(): void
    {
        $r = new DigitalRandomizer();
        $this->assertContains($r->digital1or0(), [0, 1]);
    }
}
