<?php

namespace App\Controller;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/record')]
final class RecordController extends AbstractController
{
    #[Route('/new/activity/{id}', name: 'record_new')]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        // if ($request->getMethod() == 'POST') {
            $amount = $request->request->get('amount', 0);

            $sql = "INSERT INTO records (amount, activity_id, created_at, updated_at) 
                VALUES (:amount, :activityId, :createdAt, :updatedAt)";

            $entityManager->getConnection()->executeQuery($sql , [
                'amount' => $amount,
                'activityId' => $id,
                'createdAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            ]);

            $sql = 'SELECT * FROM activities WHERE id = :id';
            $activity = $entityManager->getConnection()->executeQuery($sql, [
                'id' => $id,
            ])->fetchAssociative();


            return $this->redirectToRoute('category_view', ['id' => $activity['category_id']]);
        // }

        // $sql = 'SELECT * FROM activities WHERE id = :id';
        // $activity = $entityManager->getConnection()->executeQuery($sql, [
        //     'id' => $id,
        // ])->fetchAssociative();

        // // dd($activity);

        // $sql = 'SELECT * FROM categories WHERE id = :id';
        // $category = $entityManager->getConnection()->executeQuery($sql, [
        //     'id' => $activity['category_id'],
        // ])->fetchAssociative();

        // // dd($category);

        // return $this->render('category/view.html.twig', [
        //     'categories' => $category,
        // ]);
    }
}
