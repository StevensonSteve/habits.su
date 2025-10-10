<?php

namespace App\Notification\Message;

class TelegramNotification
{
    public function __construct(
        public readonly string $message
    ) {}
}
