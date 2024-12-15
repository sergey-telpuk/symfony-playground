<?php

namespace App\Repository;

use App\Entity\Team;
use App\Entity\TeamRepositoryInterface;
use App\Enums\DivisionEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository implements TeamRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @param DivisionEnum $division
     * @return array<Team>
     */
    public function findByDivision(DivisionEnum $division): iterable
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.division = :division')
            ->setParameter('division', $division)
            ->getQuery()
            ->getResult();
    }
}
