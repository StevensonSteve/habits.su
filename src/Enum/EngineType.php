<?php

namespace App\Enum;

enum EngineType: string
{
    case DIESEL = 'diesel';
    case PETROL = 'petrol';
    case ELECTRIC = 'electric';
    case GAS = 'gas';
    case HYBRID = 'hybrid';
}
