<?php

namespace App\Tests\Selective;

use App\Service\DigitalRandomizerInterface;

class DigitalRandomizerAlwaysOneMock implements DigitalRandomizerInterface
{
    public function digital1or0(): int
    {
        return 1;
    }

}
