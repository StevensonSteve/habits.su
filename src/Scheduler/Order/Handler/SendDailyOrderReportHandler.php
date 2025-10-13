<?php

namespace App\Scheduler\Order\Handler;

use App\Scheduler\Order\Message\SendDailyOrderReport;
use App\Notification\Message\TelegramNotification;
use App\Service\Order\OrderReportService;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendDailyOrderReportHandler
{
    public function __construct(
        private MessageBusInterface $bus,
        private OrderReportService $orderReportService,
    ) {}

    public function __invoke(SendDailyOrderReport $message): void
    {
        $reportText = $this->orderReportService->generateDailyReport($message->getReportDate());
        $this->bus->dispatch(new TelegramNotification($reportText));
    }
}
