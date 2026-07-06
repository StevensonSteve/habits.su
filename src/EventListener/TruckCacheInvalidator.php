<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Cache\TruckCacheKeys;
use App\Entity\Vehicle\Truck;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Throwable;

/**
 * Подключается как Doctrine Entity Listener через
 * #[ORM\EntityListeners([...])] в сущности App\Entity\Vehicle\Truck.
 * Пустой #[AsEntityListener] нужен только для того, чтобы Doctrine брал
 * инстанс из DI-контейнера (с autowired зависимостями), а не создавал
 * его через new.
 */
#[AsEntityListener]
final readonly class TruckCacheInvalidator
{
    public function __construct(
        #[Target(TruckCacheKeys::POOL)]
        private TagAwareCacheInterface $cacheTrucks,
        private LoggerInterface $logger,
    ) {}

    public function postPersist(Truck $truck, PostPersistEventArgs $args): void
    {
        $this->invalidate($truck);
    }

    public function postUpdate(Truck $truck, PostUpdateEventArgs $args): void
    {
        $this->invalidate($truck);
    }

    public function postRemove(Truck $truck, PostRemoveEventArgs $args): void
    {
        $this->invalidate($truck);
    }

    private function invalidate(Truck $truck): void
    {
        $tags = [
            TruckCacheKeys::TAG_LIST,
            TruckCacheKeys::tagOne($truck->getId()),
        ];

        try {
            $this->cacheTrucks->invalidateTags($tags);
        } catch (Throwable $e) {
            $this->logger->warning('Не удалось инвалидировать кэш грузовиков', [
                'tags' => $tags,
                'exception' => $e,
            ]);
        }
    }
}
