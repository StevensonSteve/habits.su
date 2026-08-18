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
        $amount = $request->request->get('amount', 0);
        $date = $request->request->get('date', 0);

        $createdAt = $date 
            ? (new DateTimeImmutable($date . ' ' . date('H:i:s')))->format("Y-m-d H:i:s")
            : (new DateTimeImmutable())->format("Y-m-d H:i:s");

        $sql = "INSERT INTO records (amount, activity_id, created_at, updated_at) 
            VALUES (:amount, :activityId, :createdAt, :updatedAt)";

        $entityManager->getConnection()->executeQuery($sql , [
            'amount' => $amount,
            'activityId' => $id,
            'createdAt' => $createdAt,
            'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
        ]);

        $sql = 'SELECT * FROM activities WHERE id = :id';
        $activity = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();


        return $this->redirectToRoute('category_view', ['id' => $activity['category_id']]);
    }

    #[Route('/delete/{id}', name: 'record_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT activity_id FROM records WHERE id = :id';
        $activityId = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchOne();

        $sql = 'DELETE FROM records WHERE id = :id';
        $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ]);

        return $this->redirectToRoute('activity_view', ['id' => $activityId]);
    }
}
