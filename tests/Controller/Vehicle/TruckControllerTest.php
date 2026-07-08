<?php

namespace App\Tests\Controller\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Enum\EngineType;
use App\Service\Vehicle\TruckService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use DateTimeImmutable;

final class TruckControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $manager;

    private EntityRepository $truckRepository;

    private TruckService $truckService;

    private string $path = '/vehicle/truck/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->truckRepository = $this->manager->getRepository(Truck::class);
        $this->truckService = static::getContainer()->get(TruckService::class);

        foreach ($this->truckService->findAll() as $truck) {
            $this->truckService->delete($truck);
        }

        $this->manager->clear();
    }

    public function testIndex(): void
    {
        $this->truckService->saveNew($this->createTruck('11111111111111111', 'AA-1111'));
        $this->truckService->saveNew($this->createTruck('22222222222222222', 'BB-2222'));

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

        $truck = $this->truckRepository->findOneBy([
            'vin' => 'AB123456789012345',
        ]);
        self::assertNotNull($truck);
        self::assertSame('Renault', $truck->getBrand());
        self::assertSame(EngineType::DIESEL, $truck->getEngineType());
    }

    public function testShow(): void
    {
        $fixture = $this->createTruck('12345678901234567', '0256 HA-8');
        $this->truckService->saveNew($fixture);

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
        $this->truckService->saveNew($fixture);

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
        $this->truckService->saveNew($fixture);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/vehicle/truck', Response::HTTP_SEE_OTHER);
        self::assertSame(0, $this->truckRepository->count([]));
    }

    private function createTruck(string $vin, string $licensePlate): Truck
    {
        $truck = $this->truckService->createNew();
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
