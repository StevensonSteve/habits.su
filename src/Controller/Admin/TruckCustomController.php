<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle\Truck;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[AdminRoute('/truck-custom', name: 'truck_custom')]
class TruckCustomController extends AbstractController
{
    #[Route('/admin/truck-custom', name: 'truck_custom_export')]
    //    #[AdminRoute('/export', name: 'export')]
    public function exportTrucks(EntityManagerInterface $entityManager): Response
    {
        $trucks = $entityManager->getRepository(Truck::class)->findAll();

        $csv = "VIN,Brand,Model,License Plate, Engine type\n";
        foreach ($trucks as $truck) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s\n",
                $truck->getVin() ?? '',
                $truck->getBrand() ?? '',
                $truck->getModel() ?? '',
                $truck->getLicensePlate() ?? '',
                $truck->getEngineType()->value ?? '',
            );
        }

        $response = new Response($csv);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="trucks_export.csv"');

        return $response;
    }
}
