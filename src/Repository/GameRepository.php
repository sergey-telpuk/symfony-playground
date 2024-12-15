<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GameRepositoryInterface;
use App\Entity\Team;
use App\Enums\GameTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository implements GameRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @param GameTypeEnum $gameType
     * @return iterable<Game>
     */
    public function findByGameType(GameTypeEnum $gameType): iterable
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.gameType = :gameType')
            ->setParameter('gameType', $gameType)->getQuery()->getResult();
    }


    /**
     * @param GameTypeEnum $gameType
     * @param int $count
     * @param bool $reverse
     * @return array{score: int, team: Team}
     */
    public function findWinnerScore(GameTypeEnum $gameType, bool $reverse = false, int $count = 1000): iterable
    {
        $order = $reverse ? 'DESC' : 'ASC';

        /**
         * @var $teams array{score:int,team_id:string}
         */
        $teams = $this->createQueryBuilder('g')
            ->select('SUM(CASE WHEN g.winner = g.teamOne THEN g.teamOneScore ELSE g.teamTwoScore END) AS score')
            ->addSelect("(SELECT t.id FROM App\Entity\Team as t WHERE t.id = g.winner) AS team_id")
            ->andWhere('g.gameType = :game_type')
            ->setParameter('game_type', $gameType)
            ->orderBy('score', $order)
            ->groupBy('g.winner')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();

        $out = [];
        foreach ($teams as $item) {
            $out [] = [
                'score' => $item['score'] ?? 0,
                'team' => $this->getEntityManager()->createQuery(
                    'SELECT t FROM App\Entity\Team t WHERE t.id = :id',
                )->setParameter('id', $item['team_id'])->getOneOrNullResult()
            ];
        }

        return $out;
    }

    /**
     * @param GameTypeEnum $gameType
     * @return iterable
     */
    public function findScoreByTeamOne(GameTypeEnum $gameType): iterable
    {
        /**
         * @var $teams array{score:int,team_id:string}
         */
        $teams = $this->createQueryBuilder('g')
            ->select('SUM(g.teamOneScore) AS score')
            ->addSelect("(SELECT t.id FROM App\Entity\Team as t WHERE t.id = g.teamOne) AS team_id")
            ->andWhere('g.gameType = :game_type')
            ->setParameter('game_type', $gameType)
            ->groupBy('g.teamOne')
            ->getQuery()
            ->getResult();

        $out = [];
        foreach ($teams as $item) {
            $out [$item['team_id']] = [
                'score' => $item['score'] ?? 0,
                'team' => $this->getEntityManager()->createQuery(
                    'SELECT t FROM App\Entity\Team t WHERE t.id = :id',
                )->setParameter('id', $item['team_id'])->getOneOrNullResult()
            ];
        }

        return $out;
    }


    /**
     * @return array<Team>
     */
    public function findResult(): iterable
    {
        yield from $this->findByGameType(gameType: GameTypeEnum::Final);
        yield from $this->findByGameType(gameType: GameTypeEnum::Semifinal);
        yield from $this->findByGameType(gameType: GameTypeEnum::PlayOff);
        yield from $this->findByGameType(gameType: GameTypeEnum::DivisionA);
        yield from $this->findByGameType(gameType: GameTypeEnum::DivisionB);
    }
}
