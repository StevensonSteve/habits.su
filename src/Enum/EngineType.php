<?php

namespace App\Enum;

enum EngineType: string
{
    case DIESEL = 'diesel';
    case PETROL = 'petrol';
    case ELECTRIC = 'electric';
    case GAS = 'gas';
    case HYBRID = 'hybrid';

    public function getLabel(): string
    {
        return match ($this) {
            self::DIESEL => 'Дизельный',
            self::PETROL => 'Бензиновый',
            self::ELECTRIC => 'Электрический',
            self::GAS => 'Газовый',
            self::HYBRID => 'Гибридный',
        };
    }
}
