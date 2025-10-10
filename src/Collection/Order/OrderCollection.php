<?php

namespace App\Collection\Order;

use App\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ArrayCollection<int, Order>
 */
class OrderCollection extends ArrayCollection
{
    public function getTotalAmount(): float
    {
        return $this->reduce(function (float $total, Order $order) {
            return $total + (float) ($order->totalAmount ?? 0);
        }, 0.0);
    }

    public function getCountByStatus(string $status): int
    {
        return $this->filter(function (Order $order) use ($status) {
            return $order->status->value === $status;
        })->count();
    }

    public function getAverageAmount(): float
    {
        if ($this->isEmpty()) {
            return 0.0;
        }

        return $this->getTotalAmount() / $this->count();
    }
}
