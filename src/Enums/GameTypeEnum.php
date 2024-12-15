<?php

declare(strict_types=1);

namespace App\Enums;

enum GameTypeEnum: string
{
    case DivisionA = 'division_a';
    case DivisionB = 'division_b';
    case PlayOff = 'play_off';
    case Semifinal = 'semifinal';
    case Final = 'final';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
