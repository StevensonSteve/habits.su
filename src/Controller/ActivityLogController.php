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

        $sql ='SELECT CAST(r.created_at AS DATE) AS date_group, r.activity_id, SUM(r.amount) AS sum, a.name, a.unit 
                FROM records AS r
                INNER JOIN activities AS a ON a.id = r.activity_id
                INNER JOIN categories AS c ON c.id = a.category_id
                WHERE c.user_id = :userId AND r.created_at >= :dateFrom AND r.created_at <= :dateTo
                GROUP BY date_group, r.activity_id, a.name, a.unit
                ORDER BY date_group DESC';
        ;

        $records = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
            'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
            'dateTo' => $dateTo->format('Y-m-d 23:59:59'),
        ])->fetchAllAssociative();

        return $this->render('activity-log/index.html.twig', [
            'activity' => $activity,
            'records' => $records,
            'filter' => $filter,
        ]);
    }
}
