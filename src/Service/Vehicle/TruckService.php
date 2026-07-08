<?php

declare(strict_types=1);

namespace App\Service\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Event\Vehicle\TruckChangedEvent;
use App\Repository\Vehicle\TruckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

final readonly class TruckService
{
    public function __construct(
        private TruckRepository $truckRepository,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function createNew(): Truck
    {
        return new Truck(Uuid::v7());
    }

    public function saveNew(Truck $truck): void
    {
        $this->entityManager->persist($truck);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new TruckChangedEvent($truck->getId()));
    }

    public function update(Truck $truck): void
    {
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new TruckChangedEvent($truck->getId()));
    }

    public function delete(Truck $truck): void
    {
        $truckId = $truck->getId();

        $this->entityManager->remove($truck);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new TruckChangedEvent($truckId));
    }

    public function find(Uuid $id): ?Truck
    {
        return $this->truckRepository->find($id);
    }

    /**
     * @return list<Truck>
     */
    public function findAll(): array
    {
        return $this->truckRepository->findAll();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Truck $truck): array
    {
        return [
            'id' => $truck->getId(),
            'vin' => $truck->getVin(),
            'brand' => $truck->getBrand(),
            'model' => $truck->getModel(),
            'manufactureDate' => $truck->getManufactureDate(),
            'mileageInitial' => $truck->getMileageInitial(),
            'engineType' => $truck->getEngineType(),
            'engineCapacity' => $truck->getEngineCapacity(),
            'engineVolume' => $truck->getEngineVolume(),
            'purchaseDate' => $truck->getPurchaseDate(),
            'color' => $truck->getColor(),
            'licensePlate' => $truck->getLicensePlate(),
            'maxWeight' => $truck->getMaxWeight(),
            'emptyWeight' => $truck->getEmptyWeight(),
            'description' => $truck->getDescription(),
            'createdAt' => $truck->getCreatedAt(),
            'updatedAt' => $truck->getUpdatedAt(),
        ];
    }

    /**
     * @param iterable<Truck> $trucks
     *
     * @return list<array<string, mixed>>
     */
    public function toArrayList(iterable $trucks): array
    {
        $result = [];

        foreach ($trucks as $truck) {
            $result[] = $this->toArray($truck);
        }

        return $result;
    }
}
