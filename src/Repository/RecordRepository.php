<?php

namespace App\Repository;

use App\Entity\Record;
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
}
