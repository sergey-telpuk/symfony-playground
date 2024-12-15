<?php

namespace App\Service;

final class DigitalRandomizer implements DigitalRandomizerInterface
{
    public function digital1or0(): int
    {
        return random_int(0, 1);
    }
}
