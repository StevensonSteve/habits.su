<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class GoalService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function create(int $activityId, int $value): void
    {
        $sql = "INSERT INTO goals (activity_id, value, created_at, updated_at)
            VALUES (:activityId, :value, :createdAt, :updatedAt)";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'activityId' => $activityId,
            'value' => $value,
            'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function getGoal(int $activityId): array
    {
        $sql = 'SELECT * 
                FROM goals AS g
                WHERE g.activity_id = :activityId
                ORDER BY g.created_at DESC';

        return $this->entityManager->getConnection()->executeQuery($sql, [
            'activityId' => $activityId,
        ])->fetchAllAssociative();
    }
}
