<?php

declare(strict_types=1);

namespace App\Event\Vehicle;

use Symfony\Component\Uid\Uuid;

final readonly class TruckChangedEvent
{
    public function __construct(
        public Uuid $truckId,
    ) {}
}
