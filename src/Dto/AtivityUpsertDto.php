<?php

declare(strict_types=1);

namespace App\Dto;

use App\Enum\ActivityUnit;

final readonly class AtivityUpsertDto
{
    public function __construct(
        public string $name, 
        public int $categoryId, 
        public ActivityUnit $unit, 
        public int $goal, 
        public bool $isDescriptionEnabled
    ) {}
}