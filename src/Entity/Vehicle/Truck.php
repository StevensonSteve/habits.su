<?php

namespace App\Entity\Vehicle;

use App\Enum\EngineType;
use App\Repository\Vehicle\TruckRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TruckRepository::class)]
#[ORM\Table(name: 'trucks')]
#[ORM\UniqueConstraint(name: 'uniq_truck_vin', columns: ['vin'])]
#[ORM\UniqueConstraint(name: 'uniq_truck_license_plate', columns: ['license_plate'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('vin', message: 'VIN номер должен быть уникальным')]
#[UniqueEntity('licensePlate', message: 'Гос. номер должен быть уникальным')]
class Truck
{
    #[ORM\Column(type: Types::STRING, length: 17)]
    #[Assert\NotBlank(message: 'VIN номер обязателен')]
    #[Assert\Length(max: 17, maxMessage: 'VIN номер не может быть длиннее {{ limit }} символов')]
    #[Assert\Regex(
        pattern: '/^[A-HJ-NPR-Z0-9]{17}$/',
        message: 'VIN номер должен содержать 17 символов (буквы и цифры)',
    )]
    private ?string $vin = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'Марка обязательна')]
    #[Assert\Length(max: 50, maxMessage: 'Марка не может быть длиннее {{ limit }} символов')]
    private ?string $brand = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    #[Assert\NotBlank(message: 'Модель обязательна')]
    #[Assert\Length(max: 50, maxMessage: 'Модель не может быть длиннее {{ limit }} символов')]
    private ?string $model = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Дата производства обязательна')]
    #[Assert\LessThanOrEqual('today', message: 'Дата производства не может быть в будущем')]
    private ?DateTimeImmutable $manufactureDate = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Начальный пробег обязателен')]
    #[Assert\PositiveOrZero(message: 'Пробег не может быть отрицательным')]
    private ?int $mileageInitial = null;

    #[ORM\Column(enumType: EngineType::class)]
    #[Assert\NotNull(message: 'Тип двигателя обязателен')]
    private ?EngineType $engineType = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank(message: 'Мощность двигателя обязательна')]
    #[Assert\Positive(message: 'Мощность должна быть положительным числом')]
    #[Assert\LessThanOrEqual(2000, message: 'Мощность не может превышать 2000 л.с.')]
    private ?int $engineCapacity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    #[Assert\NotBlank(message: 'Объем двигателя обязателен')]
    #[Assert\Positive(message: 'Объем должен быть положительным числом')]
    #[Assert\LessThanOrEqual(20, message: 'Объем не может превышать 20 литров')]
    private ?string $engineVolume = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: 'Дата покупки обязательна')]
    private ?DateTimeImmutable $purchaseDate = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: 'Цвет не может быть длиннее {{ limit }} символов')]
    private ?string $color = null;

    #[ORM\Column(type: Types::STRING, length: 15)]
    #[Assert\NotBlank(message: 'Гос. номер обязателен')]
    #[Assert\Length(max: 15, maxMessage: 'Гос. номер не может быть длиннее {{ limit }} символов')]
    private ?string $licensePlate = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Макс. масса обязательна')]
    #[Assert\Positive(message: 'Масса должна быть положительным числом')]
    private ?int $maxWeight = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: 'Снаряженная масса обязательна')]
    #[Assert\Positive(message: 'Масса должна быть положительным числом')]
    private ?int $emptyWeight = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'Описание не может быть длиннее {{ limit }} символов')]
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
