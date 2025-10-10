<?php

namespace App\Notification\Handler;

use App\Notification\Message\TelegramNotification;
use App\Service\TelegramNotifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TelegramNotificationHandler
{
    public function __construct(private TelegramNotifier $notifier) {}

    public function __invoke(TelegramNotification $notification): void
    {
        $this->notifier->send($notification->message);
    }
}
