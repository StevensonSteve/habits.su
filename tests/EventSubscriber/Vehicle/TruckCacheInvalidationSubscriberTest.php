<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber\Vehicle;

use App\Event\Vehicle\TruckChangedEvent;
use App\EventSubscriber\Vehicle\TruckCacheInvalidationSubscriber;
use App\Cache\TruckCacheKeys;
use App\Service\Vehicle\TruckCacheService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class TruckCacheInvalidationSubscriberTest extends TestCase
{
    public function testSubscribesToTruckChangedEvent(): void
    {
        self::assertSame(
            [
                TruckChangedEvent::class => 'onTruckChanged',
            ],
            TruckCacheInvalidationSubscriber::getSubscribedEvents(),
        );
    }

    public function testOnTruckChangedInvalidatesCacheForTruckId(): void
    {
        $truckId = Uuid::v7();
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('invalidateTags')
            ->with([
                TruckCacheKeys::TAG_LIST,
                TruckCacheKeys::tagOne($truckId),
            ]);

        $subscriber = new TruckCacheInvalidationSubscriber(
            new TruckCacheService($cache, $this->createMock(LoggerInterface::class)),
        );
        $subscriber->onTruckChanged(new TruckChangedEvent($truckId));
    }
}
