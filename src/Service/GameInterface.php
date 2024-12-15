<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Team;
use App\Enums\GameTypeEnum;
use Symfony\Component\Uid\Uuid;

interface GameInterface
{
    public function play(Uuid $gameId, Team $teamOne, Team $teamTwo, GameTypeEnum $gameType): void;

    public function playAllGames(): void;

    public function resetAllGames(): void;
}
