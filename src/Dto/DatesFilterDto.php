<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;

final readonly class DatesFilterDto
{
    public function __construct(
        public DateTimeImmutable $dateFrom,
        public DateTimeImmutable $dateTo,
    ) {}
}