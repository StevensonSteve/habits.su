<?php

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecordRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class DashboardService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
    ) {}

    public function getCategories(): array
    {
        $user = $this->security->getUser();

        $categories = $this->categoryRepository->getUserCategoriesSortedByLastRecord($user->getId());

        $activityCount = $this->recordRepository->getRecordSumFromToday();

        foreach ($categories as $index => $category) {
            $activities = $this->activityRepository->getLatestReportedActivitiesByCategoryId($category['id']);

            if (!isset($categories[$index]['goals'])) {
                $categories[$index]['goals'] = 0;
            }
            if (!isset($categories[$index]['goalsCompleted'])) {
                $categories[$index]['goalsCompleted'] = 0;
            }

            foreach ($activities as $activity) {
                if ($activity['goal'] > 0) {
                    $categories[$index]['goals']++;
                }
                if (isset($activityCount[$activity['id']])) {
                    if ($activityCount[$activity['id']] >= $activity['goal']) {
                        $categories[$index]['goalsCompleted']++;
                    }
                }
            }
        }

        return $categories;
    }
}
