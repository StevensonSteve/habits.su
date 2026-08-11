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
    // #[Route('/{id}', name: 'activity_index')]
    // public function index(int $id, EntityManagerInterface $entityManager): Response 
    // {
    //     $sql = 'SELECT * FROM activities WHERE category_id = :categoryId';
    //     $activities = $entityManager->getConnection()->executeQuery($sql, [
    //         'categoryId' => $id,
    //     ])->fetchAllAssociative();

    //     // dd($activities);

    //     return $this->render('activity/index.html.twig', [
    //         'activities' => $activities,
    //     ]);
    // }

    #[Route('/new/category/{id}', name: 'activity_new')]
    public function add(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');
            $unit = $request->request->get('unit');

            $sql = "INSERT INTO activities (name, category_id, unit, created_at, updated_at) 
                VALUES (:name, :categoryId, :unit, :createdAt, :updatedAt)";

            $entityManager->getConnection()->executeQuery($sql , [
                'name' => $name,
                'categoryId' => $id,
                'unit' => $unit,
                'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            ]);

            return $this->redirectToRoute('category_view', ['id' => $id]);
        }
    
        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();

        return $this->render('activity/new.html.twig', [
            'category' => $category,
        ]);
    }
}
