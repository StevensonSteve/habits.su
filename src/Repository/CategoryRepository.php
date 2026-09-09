<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getCategoryById(int $id): array|false 
    {
        $sql = 'SELECT * FROM categories WHERE id = :id';

        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();
    }
}
