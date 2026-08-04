<?php

namespace App\Entity;

use App\Repository\TodoUserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoUserRepository::class)]
#[ORM\Table(name: 'todo_users')]
#[ORM\HasLifecycleCallbacks]
class TodoUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;
    
    #[ORM\Column(length: 127)]
    private string $email;

    #[ORM\Column(length: 63)]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isAdmin = false;

    // Связь OneToMany: один пользователь имеет много задач.
    // mappedBy указывает на свойство user в сущности Todo.
    // cascade: ['persist', 'remove'] (опционально) удалит задачи, если удалить пользователя.
    #[ORM\OneToMany(targetEntity: Todo::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
    private Collection $tasks;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->tasks = new ArrayCollection(); // Инициализация коллекции
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    public function getId(): ?int
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

    /**
     * @return Collection<int, Todo>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Todo $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setUser($this); // Устанавливаем обратную связь
        }

        return $this;
    }

    public function removeTask(Todo $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // Обнуляем связь, если задача ссылалась на этого пользователя
            if ($task->getUser() === $this) {
                $task->setUser(null);
            }
        }

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
