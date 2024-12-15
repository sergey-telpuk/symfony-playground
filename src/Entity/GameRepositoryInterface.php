<?php

namespace App\Entity;

use App\Enums\GameTypeEnum;

interface GameRepositoryInterface
{
    /**
     * @param GameTypeEnum $gameType
     * @return iterable<Game>
     */
    public function findByGameType(GameTypeEnum $gameType): iterable;


    /**
     * @param GameTypeEnum $gameType
     * @param int $count
     * @param bool $reverse
     * @return array{score: int, team: Team}
     */
    public function findWinnerScore(GameTypeEnum $gameType, bool $reverse = false, int $count = 1000): iterable;

    /**
     * @param GameTypeEnum $gameType
     * @return iterable
     */
    public function findScoreByTeamOne(GameTypeEnum $gameType): iterable;


    /**
     * @return array<Team>
     */
    public function findResult(): iterable;
}
