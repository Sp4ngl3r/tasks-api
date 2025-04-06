<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in-progress';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
