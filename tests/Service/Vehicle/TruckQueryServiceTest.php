<?php

declare(strict_types=1);

namespace App\Tests\Service\Vehicle;

use App\Cache\TruckCacheKeys;
use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use App\Exception\Vehicle\TruckNotFoundException;
use App\Repository\Vehicle\TruckRepository;
use App\Service\Vehicle\TruckCacheService;
use App\Service\Vehicle\TruckQueryService;
use App\Service\Vehicle\TruckService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class TruckQueryServiceTest extends TestCase
{
    public function testGetAllForListUsesListCacheKeyAndTag(): void
    {
        $truck = $this->createTruck();
        $truckService = new TruckService(
            $this->createConfiguredMock(TruckRepository::class, [
                'findAll' => [$truck],
            ]),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
        );

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

                    $callback($item);

                    return true;
                }),
            )
            ->willReturnCallback(
                static fn(string $key, callable $callback): array => $callback(
                    new class implements ItemInterface {
                        public function getKey(): string
                        {
                            return '';
                        }

                        public function get(): mixed
                        {
                            return null;
                        }

                        public function isHit(): bool
                        {
                            return false;
                        }

                        public function set(mixed $value): static
                        {
                            return $this;
                        }

                        public function expiresAt(?\DateTimeInterface $expiration): static
                        {
                            return $this;
                        }

                        public function expiresAfter(int|\DateInterval|null $time): static
                        {
                            return $this;
                        }

                        public function tag(iterable|string $tags): static
                        {
                            return $this;
                        }

                        public function getMetadata(): array
                        {
                            return [];
                        }
                    },
                ),
            );

        $queryService = new TruckQueryService(
            $truckService,
            new TruckCacheService($cache, $this->createMock(LoggerInterface::class)),
        );

        $result = $queryService->getAllForList();

        self::assertCount(1, $result);
        self::assertSame('12345678901234567', $result[0]['vin']);
    }

    public function testGetOneForShowReturnsTruckArray(): void
    {
        $truck = $this->createTruck();
        $truckService = new TruckService(
            $this->createConfiguredMock(TruckRepository::class, [
                'find' => $truck,
            ]),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
        );

        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with(
                TruckCacheKeys::keyOne($truck->getId()),
                $this->isCallable(),
            )
            ->willReturnCallback(
                static fn(string $key, callable $callback): array => $callback(
                    new class implements ItemInterface {
                        public function getKey(): string
                        {
                            return '';
                        }

                        public function get(): mixed
                        {
                            return null;
                        }

                        public function isHit(): bool
                        {
                            return false;
                        }

                        public function set(mixed $value): static
                        {
                            return $this;
                        }

                        public function expiresAt(?\DateTimeInterface $expiration): static
                        {
                            return $this;
                        }

                        public function expiresAfter(int|\DateInterval|null $time): static
                        {
                            return $this;
                        }

                        public function tag(iterable|string $tags): static
                        {
                            return $this;
                        }

                        public function getMetadata(): array
                        {
                            return [];
                        }
                    },
                ),
            );

        $queryService = new TruckQueryService(
            $truckService,
            new TruckCacheService($cache, $this->createMock(LoggerInterface::class)),
        );

        $result = $queryService->getOneForShow($truck->getId());

        self::assertSame('Renault', $result['brand']);
        self::assertSame(EngineType::DIESEL, $result['engineType']);
    }

    public function testGetOneForShowThrowsWhenTruckMissing(): void
    {
        $missingId = Uuid::v7();
        $truckService = new TruckService(
            $this->createConfiguredMock(TruckRepository::class, [
                'find' => null,
            ]),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
        );

        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(
                static fn(string $key, callable $callback): array => $callback(
                    new class implements ItemInterface {
                        public function getKey(): string
                        {
                            return '';
                        }

                        public function get(): mixed
                        {
                            return null;
                        }

                        public function isHit(): bool
                        {
                            return false;
                        }

                        public function set(mixed $value): static
                        {
                            return $this;
                        }

                        public function expiresAt(?\DateTimeInterface $expiration): static
                        {
                            return $this;
                        }

                        public function expiresAfter(int|\DateInterval|null $time): static
                        {
                            return $this;
                        }

                        public function tag(iterable|string $tags): static
                        {
                            return $this;
                        }

                        public function getMetadata(): array
                        {
                            return [];
                        }
                    },
                ),
            );

        $queryService = new TruckQueryService(
            $truckService,
            new TruckCacheService($cache, $this->createMock(LoggerInterface::class)),
        );

        $this->expectException(TruckNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Грузовик с uuid "%s" не найден', $missingId));

        $queryService->getOneForShow($missingId);
    }

    private function createTruck(): Truck
    {
        $truck = new Truck(Uuid::v7());
        $truck->setVin('12345678901234567');
        $truck->setBrand('Renault');
        $truck->setModel('Clio');
        $truck->setManufactureDate(new DateTimeImmutable('-1 year'));
        $truck->setMileageInitial(123555);
        $truck->setEngineType(EngineType::DIESEL);
        $truck->setEngineCapacity(250);
        $truck->setEngineVolume('12.3');
        $truck->setPurchaseDate(new DateTimeImmutable());
        $truck->setColor('Red');
        $truck->setLicensePlate('AA-1111');
        $truck->setMaxWeight(2400);
        $truck->setEmptyWeight(3500);
        $truck->setDescription('Some description');

        return $truck;
    }
}
