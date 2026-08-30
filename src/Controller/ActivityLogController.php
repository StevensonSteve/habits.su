<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('activity-log')]
#[IsGranted('IS_AUTHENTICATED')] 
final class ActivityLogController extends AbstractController
{
    private const FILTER_PERIOD_TODAY = 'today';
    private const FILTER_PERIOD_YESTERDAY = 'yesterday';
    private const FILTER_PERIOD_WEEK = 'week';
    private const FILTER_PERIOD_MONTH = 'month';
    private const FILTER_PERIOD_ALL_TIME = 'all-time';

    #[Route('', name: 'activity_log_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filter = $request->request->get('filter', self::FILTER_PERIOD_TODAY);

        $now = new DateTimeImmutable();
        $dateFrom = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now->modify('-6 days'),
            self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };

        $dateTo = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now,
            self::FILTER_PERIOD_MONTH  => $now,
            self::FILTER_PERIOD_ALL_TIME    => null,
            default  => $now,
        };

        $user = $this->getUser();

        $sql = 'SELECT a.id, a.name 
            FROM activities AS a
            INNER JOIN categories AS c ON c.id = a.category_id
            WHERE c.user_id = :userId
            ORDER BY a.name ASC'
        ;
        $activity = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();

        $sql ='SELECT CAST(r.created_at AS DATE) AS date_group, r.activity_id, SUM(r.amount) AS sum, a.name 
                FROM records AS r
                INNER JOIN activities AS a ON a.id = r.activity_id
                INNER JOIN categories AS c ON c.id = a.category_id
                WHERE c.user_id = :userId AND r.created_at >= :dateFrom AND r.created_at <= :dateTo
                GROUP BY date_group, r.activity_id, a.name
                ORDER BY date_group DESC';
        ;

        // dd([
        //     'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
        //     'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        // ]);

        $records = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAllAssociative();

        // dd($records);

        return $this->render('activity-log/index.html.twig', [
            'activity' => $activity,
            'records' => $records,
            'filter' => $filter,
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
