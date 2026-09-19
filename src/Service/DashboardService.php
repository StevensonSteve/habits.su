<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ActivityRepository;
use App\Repository\CategoryRepository;
use App\Repository\RecordRepository;
use Symfony\Component\Security\Core\User\UserInterface;

final class DashboardService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
    ) {}

    public function getCategories(UserInterface $user): array
    {
        $categories = $this->categoryRepository->getUserCategoriesSortedByLastRecord($user->getId());
        $activityCount = $this->recordRepository->getRecordSumFromToday($user->getId());

        foreach ($categories as $index => $category) {
            $activities = $this->activityRepository->getActivityGoalsByCategoryId($category['id']);

            $categories[$index]['goals'] = 0;
            $categories[$index]['goalsCompleted'] = 0;

            foreach ($activities as $activity) {
                if ($activity['goal'] > 0) {
                    $categories[$index]['goals']++;
                }
                if (isset($activityCount[$activity['id']])) {
                    if (
                        $activityCount[$activity['id']] >= $activity['goal']
                        && $activity['goal'] > 0
                    ) {
                        $categories[$index]['goalsCompleted']++;
                    }
                }
            }
        }

        return $categories;
    }
}
