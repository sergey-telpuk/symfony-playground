<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Team;
use App\Enums\GameTypeEnum;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class GameService implements GameInterface
{

    public function __construct(
        private readonly EntityManagerInterface    $em,
        private readonly WinnerRandomizerInterface $winnerRandomizer,
    )
    {

    }

    public function play(Team $teamOne, Team $teamTwo, GameTypeEnum $gameType): Game
    {

        $winner = $this->winnerRandomizer->defineWinner($teamOne, $teamTwo);

        $game = new Game();
        $game->id = Uuid::v4();
        $game->teamOne = $teamOne;
        $game->teamTwo = $teamTwo;
        $game->gameType = $gameType;
        $game->playedAt = new DateTimeImmutable();

        if ($winner->equal($teamOne)) {
            $game->teamOneScore = 1;
            $game->winner = $teamOne;
        }

        if ($winner->equal($teamTwo)) {
            $game->teamTwoScore = 1;
            $game->winner = $teamTwo;
        }

        $this->em->persist($game);

        $this->em->flush();

        return $game;
    }

    public function reset(): void
    {
        $this->em->createQuery(
            'DELETE FROM App\Entity\Game'
        )->execute();
    }
}
