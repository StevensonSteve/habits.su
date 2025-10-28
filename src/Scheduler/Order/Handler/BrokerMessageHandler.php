<?php

namespace App\Scheduler\Order\Handler;

use App\Notification\Message\TelegramNotification;
use App\Scheduler\Order\Message\BrokerMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class BrokerMessageHandler
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {}

    public function __invoke(BrokerMessage $message)
    {
        $this->bus->dispatch(new TelegramNotification("Hello from BrokerMessageHandler: " . $message->content));
    }
}
