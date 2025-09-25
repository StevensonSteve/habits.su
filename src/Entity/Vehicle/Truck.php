<?php

namespace App\Entity\Vehicle;

use App\Enum\EngineType;
use App\Repository\Vehicle\TruckRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TruckRepository::class)]
#[ORM\Table(name: 'trucks')]
#[ORM\UniqueConstraint(name: 'uniq_truck_vin', columns: ['vin'])]
#[ORM\UniqueConstraint(name: 'uniq_truck_license_plate', columns: ['license_plate'])]
#[ORM\HasLifecycleCallbacks]
class Truck
{
    #[ORM\Column(type: Types::STRING, length: 17)]
    private ?string $vin = null;
    #[ORM\Column(type: Types::STRING, length: 50)]
    private ?string $brand = null;
    #[ORM\Column(type: Types::STRING, length: 50)]
    private ?string $model = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $manufactureDate = null;
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $mileageInitial = null;
    #[ORM\Column(enumType: EngineType::class)]
    private ?EngineType $engineType = null;
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $engineCapacity = null;
    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $engineVolume = null;
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $purchaseDate = null;
    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    private ?string $color = null;
    #[ORM\Column(type: Types::STRING, length: 15)]
    private ?string $licensePlate = null;
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $maxWeight = null;
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $emptyWeight = null;
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME)]
        #[ORM\GeneratedValue(strategy: 'NONE')]
        private Uuid $id,
    ) {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = clone $this->createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): void
    {
        $this->vin = $vin;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    public function getManufactureDate(): ?DateTimeImmutable
    {
        return $this->manufactureDate;
    }

    public function setManufactureDate(?DateTimeImmutable $manufactureDate): void
    {
        $this->manufactureDate = $manufactureDate;
    }

    public function getMileageInitial(): ?int
    {
        return $this->mileageInitial;
    }

    public function setMileageInitial(?int $mileageInitial): void
    {
        $this->mileageInitial = $mileageInitial;
    }

    public function getEngineType(): ?EngineType
    {
        return $this->engineType;
    }

    public function setEngineType(?EngineType $engineType): void
    {
        $this->engineType = $engineType;
    }

    public function getEngineCapacity(): ?int
    {
        return $this->engineCapacity;
    }

    public function setEngineCapacity(?int $engineCapacity): void
    {
        $this->engineCapacity = $engineCapacity;
    }

    public function getEngineVolume(): ?string
    {
        return $this->engineVolume;
    }

    public function setEngineVolume(?string $engineVolume): void
    {
        $this->engineVolume = $engineVolume;
    }

    public function getPurchaseDate(): ?DateTimeImmutable
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(?DateTimeImmutable $purchaseDate): void
    {
        $this->purchaseDate = $purchaseDate;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    public function getLicensePlate(): ?string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(?string $licensePlate): void
    {
        $this->licensePlate = $licensePlate;
    }

    public function getMaxWeight(): ?int
    {
        return $this->maxWeight;
    }

    public function setMaxWeight(?int $maxWeight): void
    {
        $this->maxWeight = $maxWeight;
    }

    public function getEmptyWeight(): ?int
    {
        return $this->emptyWeight;
    }

    public function setEmptyWeight(?int $emptyWeight): void
    {
        $this->emptyWeight = $emptyWeight;
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
