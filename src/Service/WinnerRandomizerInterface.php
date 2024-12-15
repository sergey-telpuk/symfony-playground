<?php

namespace App\Service;

use App\Entity\Team;

interface WinnerRandomizerInterface
{
    public function defineWinner(Team $teamOne, Team $teamTwo): Team;
}
