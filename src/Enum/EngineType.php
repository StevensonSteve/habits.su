<?php

namespace App\Enum;

enum EngineType: string
{
    case DIESEL = 'diesel';
    case PETROL = 'petrol';
    case ELECTRIC = 'electric';
    case GAS = 'gas';
    case HYBRID = 'hybrid';

    public static function getCases(): array
    {
        return self::cases();
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
