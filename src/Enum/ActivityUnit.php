<?php

declare(strict_types=1);

namespace App\Enum;

enum ActivityUnit: int
{
    case COUNT = 1;
    case KILOMETERS = 2;
    case MINUTES = 3;
    case PAGES = 4;

    public function label(): string
    {
        return match ($this) {
            self::COUNT => 'раз',
            self::KILOMETERS => 'км',
            self::MINUTES => 'мин',
            self::PAGES => 'стр',
        };
    }
}
