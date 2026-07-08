<?php

declare(strict_types=1);

namespace App\Exception\Vehicle;

use RuntimeException;
use Symfony\Component\Uid\Uuid;

final class TruckNotFoundException extends RuntimeException
{
    public function __construct(Uuid $id)
    {
        parent::__construct(sprintf('Грузовик с uuid "%s" не найден', $id));
    }
}
