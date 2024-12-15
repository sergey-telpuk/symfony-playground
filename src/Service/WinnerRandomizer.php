<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;

final class WinnerRandomizer implements WinnerRandomizerInterface
{
    public function __construct(
        private readonly DigitalRandomizerInterface $digitalRandomizer,
    )
    {

    }

    public function defineWinner(Team $teamOne, Team $teamTwo): Team
    {
        $random = $this->digitalRandomizer->digital1or0();

        if ($random === 0) {
            return $teamOne;
        }


        return $teamTwo;

    }
}
