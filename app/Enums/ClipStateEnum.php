<?php

namespace App\Enums;

enum ClipStateEnum: int
{
    case Ok = 1;
    case Suspicious = 2;
    case Disable = 3;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
