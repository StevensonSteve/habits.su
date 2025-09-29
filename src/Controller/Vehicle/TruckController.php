<?php

namespace App\Controller\Vehicle;

use App\Entity\Vehicle\Truck;
use App\Form\Vehicle\TruckType;
use App\Repository\Vehicle\TruckRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/vehicle/truck')]
final class TruckController extends AbstractController
{
    #[Route(name: 'app_vehicle_truck_index', methods: ['GET'])]
    public function index(TruckRepository $truckRepository): Response
    {
        return $this->render('vehicle/truck/index.html.twig', [
            'trucks' => $truckRepository->findAll(),
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

    #[Route('/{id}', name: 'app_vehicle_truck_show', methods: ['GET'])]
    public function show(Truck $truck): Response
    {
        return $this->render('vehicle/truck/show.html.twig', [
            'truck' => $truck,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_truck_edit', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_vehicle_truck_delete', methods: ['POST'])]
    public function delete(Request $request, Truck $truck, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $truck->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($truck);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicle_truck_index', [], Response::HTTP_SEE_OTHER);
    }
}
