<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');

        for ($i = 0; $i < 10; $i++) {
            $client = new Client(Uuid::v7());
            $client->name = $faker->company();
            $client->contactPerson = $faker->name();
            $client->phone = $faker->phoneNumber();
            $client->email = $faker->unique()->companyEmail();
            $client->address = $faker->address();
            $client->taxNumber = $faker->unique()->numerify('BY##########');
            $client->paymentTerms = $faker->sentence();

            $manager->persist($client);
        }

        $manager->flush();
    }
}
