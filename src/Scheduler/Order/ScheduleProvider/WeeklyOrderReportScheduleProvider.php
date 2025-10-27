<?php

namespace App\Scheduler\Order\ScheduleProvider;

use App\Scheduler\Order\Message\SendWeeklyOrderReport;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('weekly_order_report')]
class WeeklyOrderReportScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::cron('*/30 * * * *', new SendWeeklyOrderReport()),
            )
            ->stateful($this->cache)
            ->processOnlyLastMissedRun(true);
    }
}
