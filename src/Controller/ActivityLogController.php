<?php

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
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

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
    ) {}

    #[Route('', name: 'activity_log_index')]
    public function index(Request $request): Response
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

        $activity = $this->activityRepository->getActivityByUserId($user->getId());

        $recordSums = $this->activityRepository->getAmountSums(
            $user->getId(),
            $dateFrom,
            $dateTo
        );

        $records = [];

        if (!in_array($filter, [self::FILTER_PERIOD_TODAY, self::FILTER_PERIOD_YESTERDAY])) {
            $records = $this->recordRepository
            ->getActivityAndAmountSum($user->getId(), $dateFrom, $dateTo);
        }
        
        return $this->render('activity-log/index.html.twig', [
            'activity' => $activity,
            'recordSums' => $recordSums,
            'records' => $records,
            'filter' => $filter,
        ]);
    }
}
