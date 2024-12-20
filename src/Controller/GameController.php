<?php

declare(strict_types=1);

namespace App\Controller;

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

    #[Route('/games/play', name: 'play', methods: ['GET'])]
    public function play(): Response
    {
        $this->game->resetAllGames();

        $this->game->playAllGames();

        return $this->redirectToRoute('games');
    }

    #[Route('/games', name: 'games', methods: ['GET'])]
    public function index(): Response
    {
        $out = [];
        $divisionA = $this->teamRepo->findByDivision(division: DivisionEnum::A);
        $gamesDivisionA = $this->gameRepo->findScoreByTeamOne(GameTypeEnum::DivisionA);
        foreach ($divisionA as $team) {
            $out['division_a'][] = [
                'name' => $team->name->value,
                'score' => $gamesDivisionA[$team->id->toString()]['score'] ?? 0
            ];
        }

        $divisionB = $this->teamRepo->findByDivision(division: DivisionEnum::B);
        $gamesDivisionB = $this->gameRepo->findScoreByTeamOne(GameTypeEnum::DivisionB);
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
                'team_one_score' => $game->teamOneScore,
                'team_one_division' => $game->teamOne->division->value,
                'team_two_name' => $game->teamTwo->name->value,
                'team_two_score' => $game->teamTwoScore,
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
