<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\GameRepositoryInterface;
use App\Entity\Team;
use App\Entity\TeamRepositoryInterface;
use App\Enums\DivisionEnum;
use App\Enums\GameTypeEnum;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final class GameService implements GameInterface
{
    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly WinnerRandomizerInterface $winnerRandomizer,
        private readonly TeamRepositoryInterface $teamRepo,
        private readonly GameRepositoryInterface $gameRepo,
    )
    {

    }

    public function playAllGames(): void
    {
        /**
         * @var $division array<Team>
         */
        foreach ([
                     [$this->teamRepo->findByDivision(DivisionEnum::A), GameTypeEnum::DivisionA],
                     [$this->teamRepo->findByDivision(DivisionEnum::B), GameTypeEnum::DivisionB]
                 ] as [$teams, $gameType]) {
            foreach ($teams as $teamOne) {
                foreach ($teams as $teamTwo) {
                    if ($teamOne->equal($teamTwo)) {
                        continue;
                    }
                    $this->play(gameId: Uuid::v4(), teamOne: $teamOne, teamTwo: $teamTwo, gameType: $gameType);
                }
            }
        }


        $top4WinnersA = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::DivisionA, reverse: false, count: 4);
        $top4WinnersB = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::DivisionB, reverse: true, count: 4);

        //play playoff
        foreach ($top4WinnersA as $key => ['team' => $teamOne]) {
            ['team' => $teamTwo] = $top4WinnersB[$key];
            $this->play(gameId: Uuid::v4(), teamOne: $teamOne, teamTwo: $teamTwo, gameType: GameTypeEnum::PlayOff);
        }


        // play semifinal
        $topWinnerPlayoff = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::PlayOff, reverse: false, count: 4);
        $countWinnerPlayoff = count($topWinnerPlayoff);
        if ($countWinnerPlayoff % 2 != 0) {
            throw new \LogicException();
        }

        for ($i = 0; $i < $countWinnerPlayoff; $i += 2) {
            $this->play(gameId: Uuid::v4(), teamOne: $topWinnerPlayoff[$i]['team'], teamTwo: $topWinnerPlayoff[$i + 1]['team'], gameType: GameTypeEnum::Semifinal);
        }

        // play final
        $topWinnersSemifinal = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::Semifinal, reverse: false, count: 2);
        $this->play(gameId: Uuid::v4(), teamOne: $topWinnersSemifinal[0]["team"], teamTwo: $topWinnersSemifinal[1]["team"], gameType: GameTypeEnum::Final);
    }

    public function play(Uuid $gameId, Team $teamOne, Team $teamTwo, GameTypeEnum $gameType): void
    {
        $winner = $this->winnerRandomizer->defineWinner($teamOne, $teamTwo);

        $game = new Game();
        $game->id = $gameId;
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
    }

    public function resetAllGames(): void
    {
        $this->em->createQuery(
            'DELETE FROM App\Entity\Game'
        )->execute();
    }
}
