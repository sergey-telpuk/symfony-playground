<?php

declare(strict_types=1);

namespace App\Enums;

enum DivisionEnum: string
{
    case A = 'A';
    case B = 'B';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public function division(): array
    {
        return match ($this) {
            self::A => [
                TeamEnum::A,
                TeamEnum::B,
                TeamEnum::C,
                TeamEnum::D,
                TeamEnum::E,
                TeamEnum::F,
                TeamEnum::G,
                TeamEnum::H,
            ],
            self::B => [
                TeamEnum::I,
                TeamEnum::J,
                TeamEnum::K,
                TeamEnum::L,
                TeamEnum::M,
                TeamEnum::N,
                TeamEnum::O,
                TeamEnum::P,
            ]
        };

    }
}
