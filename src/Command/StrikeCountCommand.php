<?php

namespace App\Command;

use App\Repository\ActivityRepository;
use App\Repository\RecordRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

// ToDo доделать
#[AsCommand(name: 'app:strike-count')]
class StrikeCountCommand
{
    private const FAST_BUTTON_AMOUNT = 4;

    public function __construct(
        private readonly ActivityRepository $activityRepository,
        private readonly RecordRepository $recordRepository,
    ) {}

    public function __invoke(): int
    {
        $activityCount = $this->recordRepository->getAllRecordSumFromYesterday();
        $activities = $this->activityRepository->getAllActivity();

        foreach ($activities as $index => $activity) {
            $popularRecords = $this->recordRepository->getPopularRecordsByActivityId($activity['id'], self::FAST_BUTTON_AMOUNT);

            $activities[$index]['strike'] = $strikes[$activity['id']] ?? 0;
            $activities[$index]['count'] = $activityCount[$activity['id']] ?? 0;
            // $activities[$index]['popularRecords'] = $popularRecords;
        }

        return Command::SUCCESS;
    }
}
