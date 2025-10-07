<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\HasLifecycleCallbacks]
class Order
{
    #[ORM\Column]
    #[Assert\NotNull(message: 'Дата заказа обязательна для заполнения')]
    #[Assert\LessThanOrEqual(
        value: '+1 minute',
        message: 'Дата заказа не может быть в будущем',
    )]
    public DateTimeImmutable $orderDate;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Сумма заказа должна быть положительной или нулевой')]
    #[Assert\LessThanOrEqual(
        value: 999999.99,
        message: 'Сумма заказа не может превышать 999 999.99',
    )]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Сумма заказа должна быть в формате 123.45 (максимум 2 знака после запятой)',
    )]
    public ?string $totalAmount = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    #[Assert\NotNull(message: 'Статус заказа обязателен для заполнения')]
    public OrderStatus $status;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'Вес должен быть положительным или нулевым')]
    #[Assert\LessThanOrEqual(
        value: 100000,
        message: 'Вес не может превышать 100 000 кг',
    )]
    public ?int $weight = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero(message: 'Объем должен быть положительным или нулевым')]
    #[Assert\LessThanOrEqual(
        value: 10000,
        message: 'Объем не может превышать 10 000 м³',
    )]
    #[Assert\Regex(
        pattern: '/^\d+(\.\d{1,2})?$/',
        message: 'Объем должен быть в формате 123.45 (максимум 2 знака после запятой)',
    )]
    public ?string $volume = null;

    #[ORM\Column]
    public DateTimeImmutable $createdAt;

    #[ORM\Column]
    public DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Клиент обязателен для заполнения')]
    public Client $client;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private Uuid $id,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
        $this->orderDate = clone $this->createdAt;
        $this->status = OrderStatus::NEW;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return sprintf(
            'Заказ %s - %s',
            $this->orderDate->format('d.m.Y'),
            $this->client?->name ?? 'Без клиента',
        );
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
