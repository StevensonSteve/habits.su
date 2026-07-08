<?php

declare(strict_types=1);

namespace App\Service\Vehicle;

use App\Cache\TruckCacheKeys;
use App\Exception\Vehicle\TruckNotFoundException;
use Symfony\Component\Uid\Uuid;

final readonly class TruckQueryService
{
    public function __construct(
        private TruckService $truckService,
        private TruckCacheService $truckCacheService,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function getAllForList(): array
    {
        return $this->truckCacheService->get(
            TruckCacheKeys::KEY_LIST_ALL,
            [TruckCacheKeys::TAG_LIST],
            fn(): array => $this->truckService->toArrayList($this->truckService->findAll()),
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws TruckNotFoundException
     */
    public function getOneForShow(Uuid $id): array
    {
        return $this->truckCacheService->get(
            TruckCacheKeys::keyOne($id),
            [TruckCacheKeys::tagOne($id)],
            function () use ($id): array {
                $truck = $this->truckService->find($id);

                if ($truck === null) {
                    throw new TruckNotFoundException($id);
                }

                return $this->truckService->toArray($truck);
            },
        );
    }
}
