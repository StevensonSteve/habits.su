<?php

declare(strict_types=1);

namespace App\Tests\Service\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use App\Event\Vehicle\TruckChangedEvent;
use App\Repository\Vehicle\TruckRepository;
use App\Service\Vehicle\TruckService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

final class TruckServiceTest extends TestCase
{
    public function testCreateNewReturnsTruckWithUuid(): void
    {
        $service = $this->createService();

        $truck = $service->createNew();

        self::assertInstanceOf(Truck::class, $truck);
        self::assertInstanceOf(Uuid::class, $truck->getId());
    }

    #[DataProvider('mutationMethodProvider')]
    public function testMutationDispatchesTruckChangedEvent(string $method, ?string $entityManagerMethod): void
    {
        $truck = $this->createTruck();
        $entityManager = $this->createMock(EntityManagerInterface::class);

        if ($entityManagerMethod !== null) {
            $entityManager->expects($this->once())->method($entityManagerMethod)->with($truck);
        }

        $entityManager->expects($this->once())->method('flush');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())
            ->method('dispatch')
            ->with(self::callback(
                static fn(TruckChangedEvent $event): bool => $event->truckId->equals($truck->getId()),
            ));

        $service = new TruckService(
            $this->createMock(TruckRepository::class),
            $entityManager,
            $dispatcher,
        );

        $service->{$method}($truck);
    }

    /**
     * @return iterable<string, array{0: string, 1: ?string}>
     */
    public static function mutationMethodProvider(): iterable
    {
        yield 'save new' => ['saveNew', 'persist'];
        yield 'update' => ['update', null];
        yield 'delete' => ['delete', 'remove'];
    }

    public function testToArrayMapsEntityFields(): void
    {
        $truck = $this->createTruck();
        $service = $this->createService();

        $data = $service->toArray($truck);

        self::assertSame($truck->getId(), $data['id']);
        self::assertSame('12345678901234567', $data['vin']);
        self::assertSame('Renault', $data['brand']);
        self::assertSame(EngineType::DIESEL, $data['engineType']);
        self::assertSame('AA-1111', $data['licensePlate']);
    }

    public function testToArrayListMapsEveryTruck(): void
    {
        $first = $this->createTruck();
        $second = $this->createTruck('22222222222222222', 'BB-2222');
        $service = $this->createService();

        $data = $service->toArrayList([$first, $second]);

        self::assertCount(2, $data);
        self::assertSame('12345678901234567', $data[0]['vin']);
        self::assertSame('22222222222222222', $data[1]['vin']);
    }

    private function createService(): TruckService
    {
        return new TruckService(
            $this->createMock(TruckRepository::class),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
        );
    }

    private function createTruck(string $vin = '12345678901234567', string $licensePlate = 'AA-1111'): Truck
    {
        $truck = new Truck(Uuid::v7());
        $truck->setVin($vin);
        $truck->setBrand('Renault');
        $truck->setModel('Clio');
        $truck->setManufactureDate(new DateTimeImmutable('-1 year'));
        $truck->setMileageInitial(123555);
        $truck->setEngineType(EngineType::DIESEL);
        $truck->setEngineCapacity(250);
        $truck->setEngineVolume('12.3');
        $truck->setPurchaseDate(new DateTimeImmutable());
        $truck->setColor('Red');
        $truck->setLicensePlate($licensePlate);
        $truck->setMaxWeight(2400);
        $truck->setEmptyWeight(3500);
        $truck->setDescription('Some description');

        return $truck;
    }
}
