<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Team;
use App\Enums\DivisionEnum;
use App\Enums\GameTypeEnum;
use App\Repository\GameRepository;
use App\Repository\TeamRepository;
use App\Service\GameInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameController extends AbstractController
{
    public function __construct(
        private readonly GameInterface  $game,
        private readonly GameRepository $gameRepo,
        private readonly TeamRepository $teamRepo,
    )
    {
    }

    #[Route('/game/play', name: 'play', methods: ['GET'])]
    public function play(): Response
    {

        // from begin
        $this->game->reset();

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
                    $this->game->play($teamOne, $teamTwo, $gameType);
                }
            }
        }


        $top4WinnersA = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::DivisionA, reverse: false, count: 4);
        $top4WinnersB = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::DivisionB, reverse: true, count: 4);

        //play playoff
        foreach ($top4WinnersA as $key => ['team' => $teamOne]) {
            ['team' => $teamTwo] = $top4WinnersB[$key];
            $this->game->play($teamOne, $teamTwo, GameTypeEnum::PlayOff);
        }


        // play semifinal
        $topWinnerPlayoff = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::PlayOff, reverse: false, count: 4);
        $countWinnerPlayoff = count($topWinnerPlayoff);
        if ($countWinnerPlayoff % 2 != 0) {
            throw new \LogicException();
        }

        for ($i = 0; $i < $countWinnerPlayoff; $i += 2) {
            $this->game->play($topWinnerPlayoff[$i]['team'], $topWinnerPlayoff[$i + 1]['team'], GameTypeEnum::Semifinal);
        }

        // play final
        $topWinnersSemifinal = $this->gameRepo->findWinnerScore(gameType: GameTypeEnum::Semifinal, reverse: false, count: 2);
        $this->game->play($topWinnersSemifinal[0]["team"], $topWinnersSemifinal[1]["team"], GameTypeEnum::Final);

        return $this->redirectToRoute('games');
    }

    #[Route('/games', name: 'games', methods: ['GET'])]
    public function index(): Response
    {


        $out = [];
        $divisionA = $this->teamRepo->findByDivision(division: DivisionEnum::A);
        $gamesDivisionA = $this->gameRepo->findWinnerScoreByTeamOne(GameTypeEnum::DivisionA);
        foreach ($divisionA as $team) {
            $out['division_a'][] = [
                'name' => $team->name->value,
                'score' => $gamesDivisionA[$team->id->toString()]['score'] ?? 0
            ];
        }

        $divisionB = $this->teamRepo->findByDivision(division: DivisionEnum::B);
        $gamesDivisionB = $this->gameRepo->findWinnerScoreByTeamOne(GameTypeEnum::DivisionB);
        foreach ($divisionB as $team) {
            $out['division_b'][] = [
                'name' => $team->name->value,
                'score' => $gamesDivisionB[$team->id->toString()]['score'] ?? 0
            ];
        }


        $gameDivisionA = $this->gameRepo->findByGameType(gameType: GameTypeEnum::DivisionA);
        foreach ($gameDivisionA as $gameDivision) {
            if (is_null($gameDivision->winner)) {
                continue;
            }

            $out['game_division_a'][$gameDivision->teamOne->name->value . $gameDivision->teamTwo->name->value] =
                $gameDivision->teamOneScore . ':' . $gameDivision->teamTwoScore;
        }

        $gameDivisionB = $this->gameRepo->findByGameType(gameType: GameTypeEnum::DivisionB);
        foreach ($gameDivisionB as $gameDivision) {
            if (is_null($gameDivision->winner)) {
                continue;
            }

            $out['game_division_b'][$gameDivision->teamOne->name->value . $gameDivision->teamTwo->name->value] =
                $gameDivision->teamOneScore . ':' . $gameDivision->teamTwoScore;
        }


        $out['game_playoff'] = [];
        $playoffGames = $this->gameRepo->findByGameType(GameTypeEnum::PlayOff);
        foreach ($playoffGames as $game) {
            $out['game_playoff'][] = [
                'team_one_name' => $game->teamOne->name->value,
                'team_one_division' => $game->teamOne->division->value,
                'team_two_name' => $game->teamTwo->name->value,
                'team_two_division' => $game->teamTwo->division->value,
                'score' => $game->teamOneScore . ':' . $game->teamTwoScore,
            ];
        }


        $out['game_semifinal'] = [];
        $playoffGames = $this->gameRepo->findByGameType(GameTypeEnum::Semifinal);
        foreach ($playoffGames as $game) {
            $out['game_semifinal'][] = [
                'team_one_name' => $game->teamOne->name->value,
                'team_one_division' => $game->teamOne->division->value,
                'team_two_name' => $game->teamTwo->name->value,
                'team_two_division' => $game->teamTwo->division->value,
                'score' => $game->teamOneScore . ':' . $game->teamTwoScore,
            ];
        }

        $out['game_final'] = [];
        $playoffGames = $this->gameRepo->findByGameType(GameTypeEnum::Final);
        foreach ($playoffGames as $game) {
            $out['game_final'][] = [
                'team_one_name' => $game->teamOne->name->value,
                'team_one_division' => $game->teamOne->division->value,
                'team_two_name' => $game->teamTwo->name->value,
                'team_two_division' => $game->teamTwo->division->value,
                'score' => $game->teamOneScore . ':' . $game->teamTwoScore,
            ];
        }

        $out['result'] = [];


        foreach ($this->gameRepo->findResult() as $game) {
            if (isset($out['result'][$game->winner->id->toString()])) {
                continue;
            }
            $out['result'][$game->winner->id->toString()] = [
                'team_name' => $game->winner->name->value,
                'team_division' => $game->winner->division->value,
            ];
        }

        return $this->render('game.html.twig', $out);
    }
}
