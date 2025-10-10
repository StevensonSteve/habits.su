<?php

namespace App\Scheduler\Order\Message;

use DateTimeImmutable;

class SendDailyOrderReport
{
    public function __construct(
        public readonly DateTimeImmutable $reportDate
    ) {}
}
