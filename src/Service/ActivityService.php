<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\ActivityUnit;
use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ActivityService
{
    private const FAST_BUTTON_AMOUNT = 4;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly StrikeService $strikeService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function create(string $name, int $id, ActivityUnit $unit, int $goal, bool $isDescriptionEnabled): void
    {
        $sql = "INSERT INTO activities (name, category_id, unit, goal, description_enabled, created_at, updated_at)
            VALUES (:name, :categoryId, :unit, :goal, :isDescriptionEnabled, :createdAt, :updatedAt)";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'name' => $name,
            'categoryId' => $id,
            'unit' => $unit->value,
            'goal' => $goal,
            'isDescriptionEnabled' => (int) $isDescriptionEnabled,
            'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function update(string $name, int $id, ActivityUnit $unit, int $goal, int $categoryId, bool $isDescriptionEnabled): void
    {
        $sql = "UPDATE activities 
            SET name = :name, unit = :unit, goal = :goal, category_id = :categoryId, description_enabled = :isDescriptionEnabled, updated_at = :updatedAt
            WHERE id = :id";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
            'name' => $name,
            'unit' => $unit->value,
            'goal' => $goal,
            'categoryId' => $categoryId,
            'isDescriptionEnabled' => (int) $isDescriptionEnabled,
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM activities WHERE id = :id';
        $this->entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ]);
    }

    public function getActivitiesWithStats(int $categoryId, UserInterface $user): array
    {
        $activityCount = $this->recordRepository->getRecordSumFromToday($user->getId());
        $activities = $this->activityRepository->getLatestReportedActivitiesByCategoryId($categoryId);
        $strikes = $this->strikeService->getStrikes($categoryId);

        foreach ($activities as $index => $activity) {
            $popularRecords = $this->recordRepository->getPopularRecordsByActivityId($activity['id'], self::FAST_BUTTON_AMOUNT);

            $activities[$index]['strike'] = $strikes[$activity['id']] ?? 0;
            $activities[$index]['count'] = $activityCount[$activity['id']] ?? 0;
            $activities[$index]['popularRecords'] = $popularRecords;
        }

        return $activities;
    }
}
