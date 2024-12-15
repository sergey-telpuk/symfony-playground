<?php

// src/Service/GameService.php
namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Enums\GameTypeEnum;

interface GameInterface
{
    public function play(Team $teamOne, Team $teamTwo, GameTypeEnum $gameType): Game;

    public function reset(): void;
}
