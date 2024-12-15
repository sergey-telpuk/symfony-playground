<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enums\GameTypeEnum;
use App\Repository\GameRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: '`games`')]
class Game
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public Uuid $id;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    public Team $teamOne;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    public Team $teamTwo;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    public ?Team $winner = null;
    #[ORM\Column(type: "integer")]
    public int $teamOneScore = 0;
    #[ORM\Column(type: "integer")]
    public int $teamTwoScore = 0;

    #[ORM\Column(length: 255)]
    public GameTypeEnum $gameType;
    #[ORM\Column(type: 'datetime')]
    public DateTimeInterface $playedAt;
}
