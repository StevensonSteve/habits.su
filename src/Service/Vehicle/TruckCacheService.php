<?php

declare(strict_types=1);

namespace App\Service\Vehicle;

use App\Cache\TruckCacheKeys;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

final readonly class TruckCacheService
{
    public function __construct(
        #[Target(TruckCacheKeys::POOL)]
        private TagAwareCacheInterface $cache,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param list<string> $tags
     */
    public function get(string $key, array $tags, callable $callback): mixed
    {
        return $this->cache->get(
            $key,
            static function (ItemInterface $item) use ($tags, $callback): mixed {
                $item->tag($tags);

                return $callback();
            },
        );
    }

    public function invalidate(Uuid $truckId): void
    {
        $tags = [
            TruckCacheKeys::TAG_LIST,
            TruckCacheKeys::tagOne($truckId),
        ];

        try {
            $this->cache->invalidateTags($tags);
        } catch (Throwable $e) {
            $this->logger->warning('Не удалось инвалидировать кэш грузовиков', [
                'tags' => $tags,
                'exception' => $e,
            ]);
        }
    }
}
