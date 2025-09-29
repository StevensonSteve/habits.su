<?php

namespace App\Tests\Controller\Vehicle;

use App\Entity\Vehicle\Truck;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Truck index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'truck[createdAt]' => 'Testing',
            'truck[updatedAt]' => 'Testing',
            'truck[id]' => 'Testing',
            'truck[vin]' => 'Testing',
            'truck[brand]' => 'Testing',
            'truck[model]' => 'Testing',
            'truck[manufactureDate]' => 'Testing',
            'truck[mileageInitial]' => 'Testing',
            'truck[engineType]' => 'Testing',
            'truck[engineCapacity]' => 'Testing',
            'truck[engineVolume]' => 'Testing',
            'truck[purchaseDate]' => 'Testing',
            'truck[color]' => 'Testing',
            'truck[licensePlate]' => 'Testing',
            'truck[maxWeight]' => 'Testing',
            'truck[emptyWeight]' => 'Testing',
            'truck[description]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->truckRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Truck();
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setId('My Title');
        $fixture->setVin('My Title');
        $fixture->setBrand('My Title');
        $fixture->setModel('My Title');
        $fixture->setManufactureDate('My Title');
        $fixture->setMileageInitial('My Title');
        $fixture->setEngineType('My Title');
        $fixture->setEngineCapacity('My Title');
        $fixture->setEngineVolume('My Title');
        $fixture->setPurchaseDate('My Title');
        $fixture->setColor('My Title');
        $fixture->setLicensePlate('My Title');
        $fixture->setMaxWeight('My Title');
        $fixture->setEmptyWeight('My Title');
        $fixture->setDescription('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Truck');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Truck();
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setId('Value');
        $fixture->setVin('Value');
        $fixture->setBrand('Value');
        $fixture->setModel('Value');
        $fixture->setManufactureDate('Value');
        $fixture->setMileageInitial('Value');
        $fixture->setEngineType('Value');
        $fixture->setEngineCapacity('Value');
        $fixture->setEngineVolume('Value');
        $fixture->setPurchaseDate('Value');
        $fixture->setColor('Value');
        $fixture->setLicensePlate('Value');
        $fixture->setMaxWeight('Value');
        $fixture->setEmptyWeight('Value');
        $fixture->setDescription('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'truck[createdAt]' => 'Something New',
            'truck[updatedAt]' => 'Something New',
            'truck[id]' => 'Something New',
            'truck[vin]' => 'Something New',
            'truck[brand]' => 'Something New',
            'truck[model]' => 'Something New',
            'truck[manufactureDate]' => 'Something New',
            'truck[mileageInitial]' => 'Something New',
            'truck[engineType]' => 'Something New',
            'truck[engineCapacity]' => 'Something New',
            'truck[engineVolume]' => 'Something New',
            'truck[purchaseDate]' => 'Something New',
            'truck[color]' => 'Something New',
            'truck[licensePlate]' => 'Something New',
            'truck[maxWeight]' => 'Something New',
            'truck[emptyWeight]' => 'Something New',
            'truck[description]' => 'Something New',
        ]);

        self::assertResponseRedirects('/vehicle/truck/');

        $fixture = $this->truckRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getId());
        self::assertSame('Something New', $fixture[0]->getVin());
        self::assertSame('Something New', $fixture[0]->getBrand());
        self::assertSame('Something New', $fixture[0]->getModel());
        self::assertSame('Something New', $fixture[0]->getManufactureDate());
        self::assertSame('Something New', $fixture[0]->getMileageInitial());
        self::assertSame('Something New', $fixture[0]->getEngineType());
        self::assertSame('Something New', $fixture[0]->getEngineCapacity());
        self::assertSame('Something New', $fixture[0]->getEngineVolume());
        self::assertSame('Something New', $fixture[0]->getPurchaseDate());
        self::assertSame('Something New', $fixture[0]->getColor());
        self::assertSame('Something New', $fixture[0]->getLicensePlate());
        self::assertSame('Something New', $fixture[0]->getMaxWeight());
        self::assertSame('Something New', $fixture[0]->getEmptyWeight());
        self::assertSame('Something New', $fixture[0]->getDescription());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Truck();
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setId('Value');
        $fixture->setVin('Value');
        $fixture->setBrand('Value');
        $fixture->setModel('Value');
        $fixture->setManufactureDate('Value');
        $fixture->setMileageInitial('Value');
        $fixture->setEngineType('Value');
        $fixture->setEngineCapacity('Value');
        $fixture->setEngineVolume('Value');
        $fixture->setPurchaseDate('Value');
        $fixture->setColor('Value');
        $fixture->setLicensePlate('Value');
        $fixture->setMaxWeight('Value');
        $fixture->setEmptyWeight('Value');
        $fixture->setDescription('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/vehicle/truck/');
        self::assertSame(0, $this->truckRepository->count([]));
    }
}
