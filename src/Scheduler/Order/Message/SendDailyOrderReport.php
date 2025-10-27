<?php

namespace App\Scheduler\Order\Message;

use App\Notification\Message\TelegramNotification;
use App\Service\Order\OrderDailyReportService;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/15 * * * *', method: 'sendTelegram')]
class SendDailyOrderReport
{
    public function __construct(
        private MessageBusInterface $bus,
        private OrderDailyReportService $orderDailyReportService,
    ) {}

    public function sendTelegram(): void
    {
        $reportText = $this->orderDailyReportService->generateDailyReport(
            new DateTimeImmutable('now', new DateTimeZone('Europe/Minsk')),
        );
        $this->bus->dispatch(new TelegramNotification($reportText));
    }
}
