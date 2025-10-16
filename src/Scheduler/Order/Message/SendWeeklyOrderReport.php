<?php

namespace App\Scheduler\Order\Message;

use DateTimeImmutable;
use DateTimeZone;

class SendWeeklyOrderReport
{
    public function __construct() {}

    public function getReportDate(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('Europe/Minsk'));
    }
}
