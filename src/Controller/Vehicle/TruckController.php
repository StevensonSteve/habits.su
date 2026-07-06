<?php

declare(strict_types=1);

namespace App\Controller\Vehicle;

use App\Cache\TruckCacheKeys;
use App\Entity\Vehicle\Truck;
use App\Form\Vehicle\TruckType;
use App\Repository\Vehicle\TruckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/vehicle/truck')]
final class TruckController extends AbstractController
{
    public function __construct(
        #[Target(TruckCacheKeys::POOL)]
        private readonly TagAwareCacheInterface $cacheTrucks,
    ) {}

    #[Route('', name: 'app_vehicle_truck_index', methods: ['GET'])]
    public function index(TruckRepository $truckRepository): Response
    {
        $trucks = $this->cacheTrucks->get(
            TruckCacheKeys::KEY_LIST_ALL,
            function (ItemInterface $item) use ($truckRepository): array {
                // Пока в кеше один ключ, тег избыточен (хватило бы delete по ключу).
                // Оставлен на будущее: когда появятся пагинация/фильтры и ключей станет много
                // (trucks_list_page_1, trucks_list_brand_X, ...), invalidateTags('trucks_list')
                // сбросит их все разом, не зная имён.
                $item->tag([TruckCacheKeys::TAG_LIST]);

                // для дашборда в будущем может понадобиться ограниченный набор полей их можно
                // напрямую указать в методе findAllAsArray облегчив и sql и кэш
                return $truckRepository->findAllAsArray();
            },
        );

        return $this->render('vehicle/truck/index.html.twig', [
            'trucks' => $trucks,
        ]);
    }

    #[Route('/new', name: 'app_vehicle_truck_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $truck = new Truck(Uuid::v7());
        $form = $this->createForm(TruckType::class, $truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($truck);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/truck/new.html.twig', [
            'truck' => $truck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_truck_show', methods: ['GET'], requirements: ['id' => Requirement::UUID])]
    public function show(Uuid $id, TruckRepository $truckRepository): Response
    {
        $truck = $this->cacheTrucks->get(
            TruckCacheKeys::keyOne($id),
            function (ItemInterface $item) use ($truckRepository, $id): array {
                $item->tag([TruckCacheKeys::tagOne($id)]);

                $truckData = $truckRepository->findOneAsArray($id);

                if ($truckData === null) {
                    throw $this->createNotFoundException('Грузовик не найден');
                }

                return $truckData;
            },
        );

        return $this->render('vehicle/truck/show.html.twig', [
            'truck' => $truck,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_truck_edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::UUID])]
    public function edit(Request $request, Truck $truck, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TruckType::class, $truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/truck/edit.html.twig', [
            'truck' => $truck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_truck_delete', methods: ['POST'], requirements: ['id' => Requirement::UUID])]
    public function delete(Request $request, Truck $truck, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $truck->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($truck);
            $entityManager->flush();}

        return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
    }
}
