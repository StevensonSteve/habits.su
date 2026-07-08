<?php

declare(strict_types=1);

namespace App\EventSubscriber\Vehicle;

use App\Event\Vehicle\TruckChangedEvent;
use App\Service\Vehicle\TruckCacheService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class TruckCacheInvalidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TruckCacheService $truckCacheService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            TruckChangedEvent::class => 'onTruckChanged',
        ];
    }

    public function onTruckChanged(TruckChangedEvent $event): void
    {
        $this->truckCacheService->invalidate($event->truckId);
    }
}
