<?php

namespace App\Tests\Controller\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use DateTimeImmutable;

final class TruckControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $manager;

    private EntityRepository $truckRepository;

    private string $path = '/vehicle/truck/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->truckRepository = $this->manager->getRepository(Truck::class);

        foreach ($this->truckRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->manager->persist($this->createTruck('11111111111111111', 'AA-1111'));
        $this->manager->persist($this->createTruck('22222222222222222', 'BB-2222'));
        $this->manager->flush();

        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Truck index');
        self::assertCount(2, $crawler->filter('table.table tbody tr'));
        self::assertSelectorTextContains('table.table', '11111111111111111');
        self::assertSelectorTextContains('table.table', 'BB-2222');
        self::assertSelectorExists('a:contains("Create new")');
    }

    public function testIndexEmpty(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseIsSuccessful();
        self::assertPageTitleContains('Truck index');
        self::assertSelectorTextContains('table.table tbody', 'no records found');
        self::assertCount(1, $crawler->filter('table.table tbody tr'));
    }

    public function testNew(): void
    {
        // toDo try Object Mother паттерн
        $this->client->request('GET', sprintf('%snew', $this->path));
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Save', [
            'truck[vin]' => 'AB123456789012345',
            'truck[brand]' => 'Renault',
            'truck[model]' => 'Master',
            'truck[manufactureDate]' => '2020-01-15T10:00',
            'truck[mileageInitial]' => '120000',
            'truck[engineType]' => EngineType::DIESEL->value,
            'truck[engineCapacity]' => '150',
            'truck[engineVolume]' => '2.30',
            'truck[purchaseDate]' => '2021-03-10T10:00',
            'truck[color]' => 'White',
            'truck[licensePlate]' => 'AA-1234',
            'truck[maxWeight]' => '3500',
            'truck[emptyWeight]' => '2000',
            'truck[description]' => 'Test truck',
        ]);

        self::assertResponseRedirects('/vehicle/truck', Response::HTTP_SEE_OTHER);
        self::assertSame(1, $this->truckRepository->count([]));

        $truck = $this->truckRepository->findOneBy(['vin' => 'AB123456789012345']);
        self::assertNotNull($truck);
        self::assertSame('Renault', $truck->getBrand());
        self::assertSame(EngineType::DIESEL, $truck->getEngineType());
    }

    public function testShow(): void
    {
        $fixture = $this->createTruck('12345678901234567', '0256 HA-8');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Truck');
        self::assertSelectorTextContains('tr:contains("Vin") td', $fixture->getVin());
        self::assertSelectorTextContains('tr:contains("Brand") td', $fixture->getBrand());
        self::assertSelectorTextContains('tr:contains("Model") td', $fixture->getModel());
        self::assertSelectorTextContains('tr:contains("LicensePlate") td', $fixture->getLicensePlate());
        self::assertSelectorTextContains('tr:contains("Color") td', $fixture->getColor());
        self::assertSelectorTextContains('tr:contains("EngineType") td', $fixture->getEngineType()->value);
        self::assertSelectorTextContains('tr:contains("MaxWeight") td', $fixture->getMaxWeight());
        self::assertSelectorTextContains('tr:contains("ManufactureDate") td', $fixture->getManufactureDate()->format('Y-m-d'));
    }

    public function testEdit(): void
    {
        $fixture = $this->createTruck('12345678901234567', '0256 HA-8');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));
        self::assertResponseIsSuccessful();

        $this->client->submitForm('Update', [
            'truck[brand]' => 'Volvo',
            'truck[color]' => 'Blue',
        ]);

        self::assertResponseRedirects('/vehicle/truck', Response::HTTP_SEE_OTHER);
        $this->manager->clear();
        $updated = $this->truckRepository->find($fixture->getId());

        self::assertNotNull('Volvo', $updated->getBrand());
        self::assertNotNull('Blue', $updated->getColor());
        self::assertNotNull('Clio', $updated->getModel());
    }

    public function testDelete(): void
    {
        $fixture = $this->createTruck('12345678901234567', '0256 HA-8');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/vehicle/truck', Response::HTTP_SEE_OTHER);
        self::assertSame(0, $this->truckRepository->count([]));
    }

    private function createTruck(string $vin, string $licensePlate): Truck
    {
        $truck = new Truck(Uuid::v7());
        $truck->setVin($vin);
        $truck->setBrand('Renault');
        $truck->setModel('Clio');
        $truck->setManufactureDate(new DateTimeImmutable('-1 year'));
        $truck->setMileageInitial(123555);
        $truck->setEngineType(EngineType::DIESEL);
        $truck->setEngineCapacity(250);
        $truck->setEngineVolume('12.3');
        $truck->setPurchaseDate(new DateTimeImmutable());
        $truck->setColor('Red');
        $truck->setLicensePlate($licensePlate);
        $truck->setMaxWeight(2400);
        $truck->setEmptyWeight(3500);
        $truck->setDescription('Some description');

        return $truck;
    }
}
