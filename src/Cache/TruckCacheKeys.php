<?php

declare(strict_types=1);

namespace App\Cache;

use Symfony\Component\Uid\Uuid;

final class TruckCacheKeys
{
    public const string POOL = 'cache.trucks';

    public const string TAG_LIST = 'trucks_list';
    public const string KEY_LIST_ALL = 'trucks_list_all';

    public static function tagOne(Uuid|string $id): string
    {
        return 'truck_' . $id;
    }

    public static function keyOne(Uuid|string $id): string
    {
        return 'truck_' . $id;
    }
}
