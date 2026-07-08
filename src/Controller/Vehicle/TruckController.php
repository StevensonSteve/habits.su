<?php

declare(strict_types=1);

namespace App\Controller\Vehicle;

use App\Exception\Vehicle\TruckNotFoundException;
use App\Form\Vehicle\TruckType;
use App\Service\Vehicle\TruckQueryService;
use App\Service\Vehicle\TruckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;
use App\Entity\Vehicle\Truck;

#[Route('/vehicle/truck')]
final class TruckController extends AbstractController
{
    public function __construct(
        private readonly TruckService $truckService,
        private readonly TruckQueryService $truckQueryService,
    ) {}

    #[Route('', name: 'app_vehicle_truck_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('vehicle/truck/index.html.twig', [
            'trucks' => $this->truckQueryService->getAllForList(),
        ]);
    }

    #[Route('/new', name: 'app_vehicle_truck_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $truck = $this->truckService->createNew();
        $form = $this->createForm(TruckType::class, $truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->truckService->saveNew($truck);

            return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/truck/new.html.twig', [
            'truck' => $truck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_truck_show', methods: ['GET'], requirements: [
        'id' => Requirement::UUID,
    ])]
    public function show(Uuid $id): Response
    {
        try {
            $truck = $this->truckQueryService->getOneForShow($id);
        } catch (TruckNotFoundException) {
            throw $this->createNotFoundException('Грузовик не найден');
        }

        return $this->render('vehicle/truck/show.html.twig', [
            'truck' => $truck,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_truck_edit', methods: ['GET', 'POST'], requirements: [
        'id' => Requirement::UUID,
    ])]
    public function edit(Request $request, Truck $truck): Response
    {
        $form = $this->createForm(TruckType::class, $truck);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->truckService->update($truck);

            return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/truck/edit.html.twig', [
            'truck' => $truck,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_vehicle_truck_delete', methods: ['POST'], requirements: [
        'id' => Requirement::UUID,
    ])]
    public function delete(Request $request, Truck $truck): Response
    {
        if ($this->isCsrfTokenValid('delete' . $truck->getId(), $request->getPayload()->getString('_token'))) {
            $this->truckService->delete($truck);
        }

        return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
    }
}
