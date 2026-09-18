<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class CategoryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {}

    public function create(string $name): void
    {
        $user = $this->security->getUser();

        $sql = "INSERT INTO categories (name, user_id, created_at, updated_at) 
            VALUES (:name, :userId, :createdAt, :updatedAt)";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'name' => $name,
            'userId' => $user->getId(),
            'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function update(int $id, string $name): void
    {
        $sql = "UPDATE categories SET name = :name, updated_at = :updatedAt WHERE id = :id";

        $this->entityManager->getConnection()->executeQuery($sql, [
            'name' => $name,
            'id' => $id,
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);
    }

    public function delete(int $id): void
    {
        $sql = 'DELETE FROM categories WHERE id = :id';

        $this->entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ]);
    }
}
