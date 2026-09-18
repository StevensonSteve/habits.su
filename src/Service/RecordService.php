<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class RecordService
{
    public function __construct(
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
}
