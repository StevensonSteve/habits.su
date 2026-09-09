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
#[IsGranted('IS_AUTHENTICATED')] 
final class DashboardController extends AbstractController
{
    private const FILTER_PERIOD_TODAY = 'today';
    private const FILTER_PERIOD_YESTERDAY = 'yesterday';
    private const FILTER_PERIOD_WEEK = 'week';
    private const FILTER_PERIOD_MONTH = 'month';
    private const FILTER_PERIOD_ALL_TIME = 'all-time';

    #[Route('', name: 'dashboard_index')]
    public function index(EntityManagerInterface $entityManager): Response 
    {
        $user = $this->getUser();

        $sql = 'SELECT c.* FROM categories AS c
                LEFT JOIN activities AS a ON c.id = a.category_id
                LEFT JOIN records AS r ON a.id = r.activity_id
                WHERE c.user_id = :userId
                GROUP BY c.id, c.name
                ORDER BY MAX(r.created_at) DESC NULLS LAST';
        $categories = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();
        
        return $this->render('dashboard/index.html.twig', [
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    #[Route('/activity-log', name: 'dashboard_activity_log')]
    public function magazine(Request $request, EntityManagerInterface $entityManager): Response
    {
        $filter = $request->request->get('filter', self::FILTER_PERIOD_WEEK);

        $now = new DateTimeImmutable();
        $dateFrom = match ($filter) {
            self::FILTER_PERIOD_TODAY   => $now,
            self::FILTER_PERIOD_YESTERDAY   => $now->modify('-1 days'),
            self::FILTER_PERIOD_WEEK   => $now->modify('-7 days'),
            self::FILTER_PERIOD_MONTH  => $now->modify('-1 month'),
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

        $sql ='SELECT r.id, r.amount, r.created_at, a.name FROM records AS r
               INNER JOIN activities AS a ON a.id = r.activity_id
               INNER JOIN categories AS c ON c.id = a.category_id
               WHERE c.user_id = :userId
               ORDER BY r.created_at DESC';

        ;
        $records = $entityManager->getConnection()->executeQuery($sql, [
            'userId' => $user->getId(),
        ])->fetchAllAssociative();

        return $this->render('dashboard/magazine.html.twig', [
            'activity' => $activity,
            'records' => $records,
            'filter' => $filter,
        ]);
    }
}
