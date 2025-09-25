<?php

namespace App\Entity\Expense;

use App\Repository\Expense\ExpenseCategoryRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExpenseCategoryRepository::class)]
#[ORM\Table(name: 'expense_categories')]
#[ORM\UniqueConstraint(name: 'unique_expense_category', columns: ['name'])]
#[ORM\HasLifecycleCallbacks]
class ExpenseCategory
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private Uuid $id,
        #[ORM\Column(length: 180)]
        private string $name,
        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $description = null,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
