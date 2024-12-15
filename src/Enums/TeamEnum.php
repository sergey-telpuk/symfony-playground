<?php

declare(strict_types=1);

namespace App\Enums;

enum TeamEnum: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case E = 'E';
    case F = 'F';
    case G = 'G';
    case H = 'H';
    case I = 'I';
    case J = 'J';
    case K = 'K';
    case L = 'L';
    case M = 'M';
    case N = 'N';
    case O = 'O';
    case P = 'P';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
