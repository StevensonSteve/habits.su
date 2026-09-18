<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final class ActivityService
{
    private const FAST_BUTTON_AMOUNT = 4;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly StrikeService $strikeService,
    ) {}

    public function getActivitiesWithStats(int $categoryId, UserInterface $user): array
    {
        $activityCount = $this->recordRepository->getRecordSumFromToday($user->getId());
        $activities = $this->activityRepository->getLatestReportedActivitiesByCategoryId($categoryId);
        $strikes = $this->strikeService->getStrikes($categoryId);

        foreach ($activities as $index => $activity) {
            $popularRecords = $this->recordRepository->getPopularRecordsByActivityId($activity['id'], self::FAST_BUTTON_AMOUNT);

            $activities[$index]['strike'] = $strikes[$activity['id']] ?? 0;
            $activities[$index]['count'] = $activityCount[$activity['id']] ?? 0;
            $activities[$index]['popularRecords'] = $popularRecords;
        }

        return $activities;
    }
}
