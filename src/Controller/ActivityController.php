<?php

namespace App\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activity')]
final class ActivityController extends AbstractController
{
    #[Route('/{id}', name: 'activity_index')]
    public function index(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM activities WHERE category_id = :categoryId';
        $activities = $entityManager->getConnection()->executeQuery($sql, [
            'categoryId' => $id,
        ])->fetchAllAssociative();

        // dd($activities);

        return $this->render('activity/index.html.twig', [
            'activities' => $activities,
        ]);
    }
}
