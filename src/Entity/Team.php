<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enums\DivisionEnum;
use App\Enums\TeamEnum;
use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: '`teams`')]
class Team
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    public Uuid $id;

    #[ORM\Column(length: 255)]
    public TeamEnum $name;

    #[ORM\Column(length: 1)]
    public DivisionEnum $division;

    public function equal(Team $other): bool
    {
        return $this->id->equals($other->id);
    }
}
