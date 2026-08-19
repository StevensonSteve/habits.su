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
    private const FILTER_PERIOD_WEEK = 'week';
    private const FILTER_PERIOD_MONTH = 'month';
    private const FILTER_PERIOD_HALF_YEAR = 'half-year';
    private const FILTER_PERIOD_YEAR = 'year';
    private const FILTER_PERIOD_ALL_TIME = 'all-time';

    #[Route('/{id}', name: 'activity_view')]
    public function view(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        $filter = $request->request->get('filter', self::FILTER_PERIOD_WEEK);

        $sql = 'SELECT * FROM activities WHERE id = :id';
        $activity = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id,
        ])->fetchAssociative();

        $now = new DateTimeImmutable();
        $dateFrom = match ($filter) {
            self::FILTER_PERIOD_WEEK   => $now->modify('-7 days'),
            self::FILTER_PERIOD_HALF_YEAR   => $now->modify('-6 months'),
            self::FILTER_PERIOD_YEAR   => $now->modify('-1 year'),
            self::FILTER_PERIOD_ALL_TIME    => null,
            self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
            default  => $now->modify('-7 days'),
        };

        $sql = 'SELECT * FROM records WHERE activity_id = :activityId AND created_at >= :dateFrom  ORDER BY created_at DESC';        
        $records = $entityManager->getConnection()->executeQuery($sql, [
            'activityId' => $activity['id'],
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        ])->fetchAllAssociative();

        $sql = 'SELECT COUNT(*) AS count
            FROM records 
            WHERE activity_id = :activityId AND created_at >= :dateFrom;
        ';
        $activityCount = $entityManager->getConnection()->executeQuery($sql, [
            'activityId' => $id,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        ])->fetchAssociative();

        $sql = 'SELECT SUM(amount) AS sum
            FROM records 
            WHERE activity_id = :activityId AND created_at >= :dateFrom;
        ';
        $activitySum = $entityManager->getConnection()->executeQuery($sql, [
            'activityId' => $id,
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        ])->fetchAssociative();

        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $activity['category_id'],
        ])->fetchAssociative(); 
        

        return $this->render('activity/view.html.twig', [
            'activity' => $activity,
            'category' => $category,
            'activityCount' => $activityCount,
            'activitySum' => $activitySum,
            'records' => $records,
            'filter' => $filter,
        ]);
    }

    #[Route('/delete/{id}', name: 'activity_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM activities WHERE id = :id';
        $activity = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ])->fetchAssociative();

        $sql = 'DELETE FROM activities WHERE id = :id';
        $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ]);

        return $this->redirectToRoute('category_view', ['id' => $activity['category_id']]);
    }

    #[Route('/new/category/{id}', name: 'activity_new')]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager): Response 
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

    #[Route('/update/{id}', name: 'activity_update')]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response 
    {
        $sql = 'SELECT * FROM activities WHERE id = :id';
        $activity = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $id
        ])->fetchAssociative();

        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');
            $unit = $request->request->get('unit');

            $sql = "UPDATE activities SET name = :name, unit = :unit, updated_at = :updatedAt WHERE id = :id";
            $entityManager->getConnection()->executeQuery($sql , [
                'id' => $id,
                'name' => $name,
                'unit' => $unit,
                'updatedAt' => new DateTimeImmutable()->format("Y-m-d H:i:s"),
            ]);

            return $this->redirectToRoute('category_view', ['id' => $activity['category_id']]);
        }
    
        $sql = 'SELECT * FROM categories WHERE id = :id';
        $category = $entityManager->getConnection()->executeQuery($sql, [
            'id' => $activity['category_id'],
        ])->fetchAssociative();

        return $this->render('activity/update.html.twig', [
            'category' => $category,
            'activity' => $activity,
        ]);
    }

}

