<?php

namespace App\Scheduler\Order\ScheduleProvider;

use App\Scheduler\Order\Message\SendDailyOrderReport;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('daily_order_report')]
class DailyOrderReportScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {}

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::cron('* * * * *', new SendDailyOrderReport()),
            )
            ->stateful($this->cache)
            ->processOnlyLastMissedRun(true);
    }
}
