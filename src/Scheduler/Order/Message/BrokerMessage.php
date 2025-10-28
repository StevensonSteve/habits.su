<?php

namespace App\Scheduler\Order\Message;

class BrokerMessage
{
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }
}
