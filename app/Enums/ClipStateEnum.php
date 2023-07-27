<?php

namespace App\Enums;

enum ClipStateEnum: string
{
    case Ok = 'ok';
    case Alive = 'alive';
    case Disable = 'disable';
    case Suspicious = 'suspicious';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
