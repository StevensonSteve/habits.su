<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Order;
use App\Enum\OrderStatus;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');

        $clients = $manager->getRepository(Client::class)->findAll();

        foreach ($clients as $client) {
            $orderCount = rand(5, 10);

            for ($i = 0; $i < $orderCount; $i++) {
                $order = new Order(Uuid::v7());
                $order->client = $client;
                $order->orderDate = DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween('-6 months', 'now'),
                );
                $order->totalAmount = number_format(
                    $faker->randomFloat(2, 100, 999999.99),
                    2,
                    '.',
                    '',
                );
                $order->status = $faker->randomElement(OrderStatus::cases());
                $order->weight = $faker->numberBetween(0, 100000);
                $order->volume = number_format(
                    $faker->randomFloat(2, 0, 10000),
                    2,
                    '.',
                    '',
                );

                $manager->persist($order);
            }
        }

        $manager->flush();
    }
}
