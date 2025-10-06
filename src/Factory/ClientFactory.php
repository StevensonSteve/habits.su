<?php

namespace App\Factory;

use App\Entity\Client;
use Symfony\Component\Uid\Uuid;

class ClientFactory
{
    public function assignId(Client $client): void
    {
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($client, Uuid::v7());
    }
}
