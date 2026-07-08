<?php

declare(strict_types=1);

namespace App\Tests\Service\Vehicle;

use App\Cache\TruckCacheKeys;
use App\Service\Vehicle\TruckCacheService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class TruckCacheServiceTest extends TestCase
{
    public function testGetTagsItemAndReturnsCallbackResult(): void
    {
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with(
                TruckCacheKeys::KEY_LIST_ALL,
                $this->callback(function (callable $callback): bool {
                    $item = $this->createMock(ItemInterface::class);
                    $item->expects($this->once())
                        ->method('tag')
                        ->with([TruckCacheKeys::TAG_LIST]);

                    self::assertSame(['payload'], $callback($item));

                    return true;
                }),
            )
            ->willReturn(['payload']);

        $service = new TruckCacheService($cache, $this->createMock(LoggerInterface::class));

        self::assertSame(['payload'], $service->get(
            TruckCacheKeys::KEY_LIST_ALL,
            [TruckCacheKeys::TAG_LIST],
            static fn(): array => ['payload'],
        ));
    }

    public function testInvalidateCallsExpectedTags(): void
    {
        $truckId = Uuid::v7();
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('invalidateTags')
            ->with([
                TruckCacheKeys::TAG_LIST,
                TruckCacheKeys::tagOne($truckId),
            ]);

        $service = new TruckCacheService($cache, $this->createMock(LoggerInterface::class));

        $service->invalidate($truckId);
    }

    public function testInvalidateLogsWarningWhenCacheFails(): void
    {
        $truckId = Uuid::v7();
        $exception = new RuntimeException('redis down');

        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('invalidateTags')
            ->willThrowException($exception);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('warning')
            ->with(
                'Не удалось инвалидировать кэш грузовиков',
                self::callback(static fn(array $context): bool => $context['exception'] === $exception),
            );

        $service = new TruckCacheService($cache, $logger);

        $service->invalidate($truckId);
    }
}
