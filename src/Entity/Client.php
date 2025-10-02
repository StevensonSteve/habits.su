<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTimeImmutable;
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
#[UniqueEntity('tax_number', message: 'Tax number номер должен быть уникальным')]
#[UniqueEntity('email', message: 'Email номер должен быть уникальным')]
class Client
{
    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column(length: 255)]
    public ?string $contactPerson = null;

    #[ORM\Column(length: 50)]
    public ?string $phone = null;

    #[ORM\Column(length: 255)]
    public ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $address = null;

    #[ORM\Column(length: 20)]
    public ?string $taxNumber = null;

    #[ORM\Column(length: 500, nullable: true)]
    public ?string $paymentTerms = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    public ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        public Uuid $id {
            get => $this->id;
        }
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
