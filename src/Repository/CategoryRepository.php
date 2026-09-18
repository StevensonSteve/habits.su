<?php

declare(strict_types=1);

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

    public function getUserCategoriesSortedByLastRecord(int $userId): array
    {
        $sql = 'SELECT c.* FROM categories AS c
            LEFT JOIN activities AS a ON c.id = a.category_id
            LEFT JOIN records AS r ON a.id = r.activity_id
            WHERE c.user_id = :userId
            GROUP BY c.id, c.name
            ORDER BY MAX(r.created_at) DESC NULLS LAST';
        return $this->getEntityManager()->getConnection()->executeQuery($sql, [
            'userId' => $userId,
        ])->fetchAllAssociative();
    }
}
