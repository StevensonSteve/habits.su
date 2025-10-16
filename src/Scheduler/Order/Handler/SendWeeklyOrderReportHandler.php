<?php

namespace App\Scheduler\Order\Handler;

use App\Notification\Message\TelegramNotification;
use App\Scheduler\Order\Message\SendWeeklyOrderReport;
use App\Service\Order\OrderWeeklyReportService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SendWeeklyOrderReportHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private OrderWeeklyReportService $orderweeklyReportService,
    ) {}

    public function __invoke(SendWeeklyOrderReport $message): void
    {
        $reportText = $this->orderweeklyReportService->generateWeeklyReport($message->getReportDate());
        $this->bus->dispatch(new TelegramNotification($reportText));
    }
}
