<?php

namespace App\Repository;

use App\Entity\Activity;
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
}
