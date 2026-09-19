<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ActivityRepository;
use App\Service\RecordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('activity-log')]
#[IsGranted('IS_AUTHENTICATED')]
final class ActivityLogController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordService $recordService,
    ) {}

    #[Route('', name: 'activity_log_index')]
    public function index(Request $request): Response
    {
        $filter = $request->request->get('filter', RecordService::FILTER_PERIOD_TODAY);

        $user = $this->getUser();
        $records = $this->recordService->getRecords($user, $filter);

        // todo rename 'activity' => $activity, TO 'activities' => $activities,
        $activity = $this->activityRepository->getActivitiesByUserId($user->getId());
        $recordSums = $this->recordService->getRecordsSum($user, $filter);

        return $this->render('activity-log/index.html.twig', [
            'activity' => $activity,
            'recordSums' => $recordSums,
            'records' => $records,
            'filter' => $filter,
        ]);
    }
}
