<?php

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

    public function getRecordSumFromToday(): array
    {

    //ToDo отсечь клиентов по id
        $today = new DateTimeImmutable('today');
        $sql = 'SELECT activity_id, SUM(amount) AS count
            FROM records    
            WHERE created_at >= :dateFrom AND created_at < :dateTo
            GROUP BY activity_id;
        ';
        
        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
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
}
