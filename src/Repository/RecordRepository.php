<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Record;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Record>
 */
class RecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    public function getPopularRecordsByActivityId(int $id, int $limit = 3): array
    {
        $sql = 'SELECT sub.amount 
                FROM (
                    SELECT r.amount AS amount
                    FROM records AS r
                    INNER JOIN activities AS a ON a.id = r.activity_id
                    WHERE r.activity_id = :id
                    GROUP BY r.amount
                    ORDER BY COUNT(r.amount) DESC 
                    LIMIT :limitRows
                ) AS sub
                ORDER BY sub.amount ASC';

        $records = $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'id' => $id,
            'limitRows' => $limit,
        ])->fetchAllAssociative();

        return $records;
    }

    public function getRecordSumFromToday(int $userId): array
    {
        $today = new DateTimeImmutable('today');
        $sql = 'SELECT r.activity_id, SUM(r.amount) AS count
            FROM records AS r
            INNER JOIN activities AS a ON a.id = r.activity_id
            INNER JOIN categories AS c ON c.id = a.category_id
            WHERE c.user_id = :userId
                AND r.created_at >= :dateFrom
                AND r.created_at < :dateTo
            GROUP BY r.activity_id;
        ';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'userId' => $userId,
            'dateFrom' => $today->format('Y-m-d 00:00:00'),
            'dateTo' => $today->modify('+1 day')->format('Y-m-d 00:00:00'),
        ])->fetchAllKeyValue();
    }

    public function getActivityId(int $id): mixed
    {
        $sql = 'SELECT activity_id FROM records WHERE id = :id';
        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchOne();
    }

    public function getActivityAndAmountSum(int $userId, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo): array
    {
        $sql = 'SELECT CAST(r.created_at AS DATE) AS date_group, r.activity_id, SUM(r.amount) AS sum, a.name, a.unit 
                    FROM records AS r
                    INNER JOIN activities AS a ON a.id = r.activity_id
                    INNER JOIN categories AS c ON c.id = a.category_id
                    WHERE c.user_id = :userId AND r.created_at >= :dateFrom AND r.created_at <= :dateTo
                    GROUP BY date_group, r.activity_id, a.name, a.unit
                    ORDER BY date_group DESC';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'userId' => $userId,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAllAssociative();
    }

    public function getRecordsByActivityId(int $activityId, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo)
    {
        $sql = 'SELECT *
            FROM records
            WHERE activity_id = :activityId 
                AND created_at >= :dateFrom 
                AND created_at <= :dateTo
            ORDER BY created_at DESC';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'activityId' => $activityId,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAllAssociative();
    }

    public function getActivityCountFromRecords(int $activityId, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo)
    {
        $sql = 'SELECT COUNT(*) AS count
            FROM records 
            WHERE activity_id = :activityId 
                AND created_at >= :dateFrom;';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'activityId' => $activityId,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAssociative();
    }

    public function getActivitySumFromFecords(int $activityId, DateTimeImmutable $dateFrom, DateTimeImmutable $dateTo)
    {
        $sql = 'SELECT SUM(amount) AS sum
            FROM records 
            WHERE activity_id = :activityId AND created_at >= :dateFrom AND created_at <= :dateTo;
        ';
        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'activityId' => $activityId,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAssociative();
    }
}
