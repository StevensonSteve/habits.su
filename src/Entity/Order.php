<?php

namespace App\Entity;

use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Column]
    public ?\DateTimeImmutable $orderDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0, nullable: true)]
    public ?string $totalAmount = null;

    #[ORM\Column(enumType: OrderStatus::class)]
    public ?OrderStatus $status = null;

    #[ORM\Column(nullable: true)]
    public ?int $weigh = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 0, nullable: true)]
    public ?string $volume = null;

    #[ORM\Column]
    public ?DateTimeImmutable $createdAt;

    #[ORM\Column]
    public ?DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    public ?Client $client = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        public Uuid $id {
            get => $this->id;
        },
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
