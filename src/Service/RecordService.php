<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DateFromToDto;
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

    private const FILTER_PERIOD_OWN = 'own';

    public const RECORD_FILTERS = [
        self::FILTER_PERIOD_TODAY => 'Сегодня',
        self::FILTER_PERIOD_YESTERDAY => 'Вчера',
        self::FILTER_PERIOD_WEEK => 'Неделя',
        self::FILTER_PERIOD_MONTH => 'С 1-го числа',
        self::FILTER_PERIOD_ALL_TIME => 'Всё время',
        self::FILTER_PERIOD_OWN => 'Свой',
    ];

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function create(float $amount, int $id, string $createdAt, string $description): void
    {
        $sql = "INSERT INTO records (amount, activity_id, description, created_at, updated_at) 
                VALUES (:amount, :activityId, :description, :createdAt, :updatedAt)";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'amount' => $amount,
            'activityId' => $id,
            'description' => $description,
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

    public function getRecords(UserInterface $user, DateFromToDto $dates): array
    {
        return $this->recordRepository
            ->getActivityAndAmountSum(
                $user->getId(),
                $dates->dateFrom,
                $dates->dateTo,  
        );
    }

    public function getRecordsSum(UserInterface $user, DateFromToDto $dates): array
    {
        return $this->activityRepository->getAmountSums(
            $user->getId(),
            $dates->dateFrom,
            $dates->dateTo,
        );
    }

    public function getDateRange(string $filter): DateFromToDto
    {
        $now = new DateTimeImmutable();
        $dateFrom = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now->modify('-6 days'),
            self::FILTER_PERIOD_MONTH  => $now->modify('first day of this month'),
            self::FILTER_PERIOD_ALL_TIME    => $now->modify('-20 years'),
            default  => $now,
        };
        $dateTo = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now,
            self::FILTER_PERIOD_MONTH  => $now,
            self::FILTER_PERIOD_ALL_TIME    => $now,
            default  => $now,
        };

        return new DateFromToDto(
            $dateFrom, 
            $dateTo
        );
    }
}
