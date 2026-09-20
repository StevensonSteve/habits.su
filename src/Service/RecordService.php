<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RecordService
{
    public const FILTER_PERIOD_TODAY = 'today';

    private const FILTER_PERIOD_YESTERDAY = 'yesterday';

    private const FILTER_PERIOD_WEEK = 'week';

    private const FILTER_PERIOD_MONTH = 'month';

    private const FILTER_PERIOD_ALL_TIME = 'all-time';

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function create(float $amount, int $id, string $createdAt): void
    {
        $sql = "INSERT INTO records (amount, activity_id, created_at, updated_at) 
                VALUES (:amount, :activityId, :createdAt, :updatedAt)";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'amount' => $amount,
            'activityId' => $id,
            'createdAt' => $createdAt,
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM records WHERE id = :id';
        $this->entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ]);
    }

    public function getRecords(UserInterface $user, string $filter): array
    {
        $records = [];

        if (! in_array($filter, [self::FILTER_PERIOD_TODAY, self::FILTER_PERIOD_YESTERDAY], true)) {
            $records = $this->recordRepository
                ->getActivityAndAmountSum(
                    $user->getId(),
                    $this->getDateFrom($filter),
                    $this->getDateTo($filter),
                );
        }

        return $records;
    }

    public function getRecordsSum(UserInterface $user, string $filter): array
    {
        return $this->activityRepository->getAmountSums(
            $user->getId(),
            $this->getDateFrom($filter),
            $this->getDateTo($filter),
        );
    }

    public function getDateRange(string $filter): array
    {
        $now = new DateTimeImmutable();
        $dateFrom = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now->modify('-6 days'),
            self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };
        $dateTo = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now,
            self::FILTER_PERIOD_MONTH  => $now,
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };

        return [$dateFrom, $dateTo];
    }

    public function getDateFrom(string $filter): DateTimeImmutable
    {
        $now = new DateTimeImmutable();
        return match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now->modify('-6 days'),
            self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };
    }

    public function getDateTo(string $filter): DateTimeImmutable
    {
        $now = new DateTimeImmutable();
        return match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now,
            self::FILTER_PERIOD_MONTH  => $now,
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };
    }
}
