<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Activity;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function getActivityById(int $id): array|false
    {
        $sql = 'SELECT * FROM activities WHERE id = :id';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();
    }

    public function getActivityGoalsByCategoryId(int $categoryId): array
    {
        $sql = 'SELECT a.id, a.goal
            FROM activities AS a
            INNER JOIN categories AS c ON a.category_id = c.id 
            WHERE a.category_id = :categoryId';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'categoryId' => $categoryId,
        ])->fetchAllAssociative();
    }

    public function getLatestReportedActivitiesByCategoryId(int $categoryId): array
    {
        $sql = 'SELECT a.* 
                FROM activities AS a
                LEFT JOIN records AS r ON a.id = r.activity_id
                WHERE a.category_id = :categoryId
                GROUP BY a.id, a.name, a.unit
                ORDER BY MAX(r.created_at) DESC NULLS LAST';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'categoryId' => $categoryId,
        ])->fetchAllAssociative();
    }

    public function getAmountSums(int $userId, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo): array|false
    {
        $sql = 'SELECT a.name, a.unit, a.id, SUM(r.amount) AS sum 
                FROM activities AS a
                INNER JOIN records AS r ON a.id = r.activity_id
                INNER JOIN categories AS c ON c.id = a.category_id
                WHERE c.user_id = :userId AND r.created_at >= :dateFrom AND r.created_at <= :dateTo
                GROUP BY a.name, a.unit, a.id
                ORDER BY SUM(r.amount) DESC';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'userId' => $userId,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAllAssociative();
    }

    public function getActivitiesByUserId(int $userId)
    {
        $sql = 'SELECT *
            FROM activities AS a
            INNER JOIN categories AS c ON c.id = a.category_id
            WHERE c.user_id = :userId
            ORDER BY a.name ASC';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'userId' => $userId,
        ])->fetchAllAssociative();
    }

    public function getActivitiesByCategoryId(int $categoryId)
    {
        $sql = 'SELECT *
            FROM activities AS a
            WHERE a.category_id = :categoryId
            ORDER BY a.name ASC';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'categoryId' => $categoryId,
        ])->fetchAllAssociative();
    }
}
