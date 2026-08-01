<?php

namespace App\Entity;

use App\Repository\TodoRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoRepository::class)]
#[ORM\Table(name: 'todos')]
#[ORM\HasLifecycleCallbacks]
class Todo
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isCompleted = false;

    // Связь ManyToOne: много задач могут принадлежать одному пользователю.
    // inversedBy указывает на свойство tasks в сущности TodoUser.
    // JoinColumn определяет имя внешнего ключа в БД (user_id) и запрещает null (задача обязана иметь юзера).
    #[ORM\ManyToOne(targetEntity: TodoUser::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?TodoUser $user = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): void
    {
        $this->isCompleted = $isCompleted;
    }

    public function getUser(): ?TodoUser
    {
        return $this->user;
    }

    public function setUser(?TodoUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
