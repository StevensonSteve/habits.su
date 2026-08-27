<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('')]
#[IsGranted('IS_AUTHENTICATED_FULLY')] 
final class DashboardController extends AbstractController
{
    private const FILTER_PERIOD_WEEK = 'week';
    private const FILTER_PERIOD_MONTH = 'month';
    private const FILTER_PERIOD_HALF_YEAR = 'half-year';
    private const FILTER_PERIOD_YEAR = 'year';
    private const FILTER_PERIOD_ALL_TIME = 'all-time';

    #[Route('', name: 'dashboard_index')]
    public function index(EntityManagerInterface $entityManager): Response 
    {
        $user = $this->getUser();

        $sql = 'SELECT * FROM categories WHERE user_id = :userId ORDER BY updated_at DESC';
        $categories = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();
        
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    #[Route('/magazine', name: 'dashboard_magazine')]
    public function magazine(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $sql = 'SELECT a.id, a.name 
                FROM activities AS a
                INNER JOIN categories AS c ON c.id = a.category_id
                WHERE c.user_id = :userId
                ORDER BY a.name ASC';

        $activities = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();

        return $this->render('dashboard/magazine.html.twig', [
            'activities' => $activities,
        ]);
    }

    // $filter = $request->request->get('filter', self::FILTER_PERIOD_WEEK);
        
        // $sql = 'SELECT * FROM activities WHERE id = :id';
        // $activity = $entityManager->getConnection()->executeQuery($sql, [
        //     'id' => $id,
        // ])->fetchAssociative();

        // $now = new DateTimeImmutable();
        // $dateFrom = match ($filter) {
        //     self::FILTER_PERIOD_WEEK   => $now->modify('-7 days'),
        //     self::FILTER_PERIOD_HALF_YEAR   => $now->modify('-6 months'),
        //     self::FILTER_PERIOD_YEAR   => $now->modify('-1 year'),
        //     self::FILTER_PERIOD_ALL_TIME    => null,
        //     self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
        //     default  => $now->modify('-7 days'),
        // };

        // $sql = 'SELECT * FROM records WHERE activity_id = :activityId AND created_at >= :dateFrom  ORDER BY created_at DESC';        
        // $records = $entityManager->getConnection()->executeQuery($sql, [
        //     'activityId' => $activity['id'],
        //     'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        // ])->fetchAllAssociative();

        // $sql = 'SELECT COUNT(*) AS count
        //     FROM records 
        //     WHERE activity_id = :activityId AND created_at >= :dateFrom;
        // ';
        // $activityCount = $entityManager->getConnection()->executeQuery($sql, [
        //     'activityId' => $id,
        //     'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        // ])->fetchAssociative();

        // $sql = 'SELECT SUM(amount) AS sum
        //     FROM records 
        //     WHERE activity_id = :activityId AND created_at >= :dateFrom;
        // ';
        // $activitySum = $entityManager->getConnection()->executeQuery($sql, [
        //     'activityId' => $id,
        //     'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        // ])->fetchAssociative();

        // $sql = 'SELECT * FROM categories WHERE id = :id';
        // $category = $entityManager->getConnection()->executeQuery($sql, [
        //     'id' => $activity['category_id'],
        // ])->fetchAssociative(); 
        

        // return $this->render('dashboard/magazine.html.twig', [
        //     'activity' => $activity,
        //     'category' => $category,
        //     'activityCount' => $activityCount,
        //     'activitySum' => $activitySum,
        //     'records' => $records,
        //     'filter' => $filter,
        // ]);
}
