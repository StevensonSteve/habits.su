<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'clients')]
#[ORM\UniqueConstraint(name: 'uniq_client_tax_number', columns: ['tax_number'])]
#[ORM\UniqueConstraint(name: 'uniq_client_email', columns: ['email'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('taxNumber', message: 'Tax number номер должен быть уникальным')]
#[UniqueEntity('email', message: 'Email номер должен быть уникальным')]
class Client
{
    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(length: 255)]
    public string $contactPerson;

    #[ORM\Column(length: 50)]
    public string $phone;

    #[ORM\Column(length: 255)]
    public string $email;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $address = null;

    #[ORM\Column(length: 20)]
    public string $taxNumber;

    #[ORM\Column(length: 500, nullable: true)]
    public ?string $paymentTerms = null;

    #[ORM\Column]
    public DateTimeImmutable $createdAt;

    #[ORM\Column]
    public DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $orders;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private Uuid $id,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
        $this->orders = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): void
    {
        if (! $this->orders->contains($order)) {
            $this->orders->add($order);
            $order->client = $this;
        }
    }

    public function removeOrder(Order $order): void
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->client === $this) {
                $order->client = null;
            }
        }
    }
}
