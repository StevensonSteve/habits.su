<?php

namespace App\Service\Order;

use App\Collection\Order\OrderCollection;
use App\Enum\OrderStatus;
use App\Repository\OrderRepository;
use DateTimeImmutable;

class OrderWeeklyReportService
{
    private const REPORT_PERIOD_HOURS = 24;

    public function __construct(
        private OrderRepository $orderRepository,
    ) {}

    public function generateWeeklyReport(DateTimeImmutable $reportDate): string
    {
        $startDate = $reportDate->modify('-' . self::REPORT_PERIOD_HOURS . ' hours');
        $orders = $this->orderRepository->findOrdersByDateRange($startDate, $reportDate);

        if ($orders->isEmpty()) {
            return $this->generateEmptyWeeklyReport($reportDate);
        }

        return $this->formatWeeklyReport($orders, $reportDate);
    }

    private function formatWeeklyReport(OrderCollection $orders, DateTimeImmutable $reportDate): string
    {
        $periodEnd = $reportDate->format('d.m.Y H:i');
        $periodStart = $reportDate->modify('-' . self::REPORT_PERIOD_HOURS . ' hours')->format('d.m.Y H:i');

        $report = "*Недельный отчет по заказам*\n";
        $report .= "Период: {$periodStart} - {$periodEnd}\n\n";

        $report .= "*Общая статистика:*\n";
        $report .= "• Всего заказов: {$orders->count()}\n";
        $report .= "• Общая сумма: " . number_format($orders->getTotalAmount(), 2) . " ₽\n";
        $report .= "• Средний чек: " . number_format($orders->getAverageAmount(), 2) . " ₽\n";

        $report .= "\n*По статусам:*\n";
        $report .= "• Новые: {$orders->getCountByStatus(OrderStatus::NEW->value)}\n";
        $report .= "• Подтвержденные: {$orders->getCountByStatus(OrderStatus::CONFIRMED->value)}\n";
        $report .= "• В работе: {$orders->getCountByStatus(OrderStatus::IN_PROGRESS->value)}\n";
        $report .= "• Завершенные: {$orders->getCountByStatus(OrderStatus::COMPLETED->value)}\n";
        $report .= "• Отмененные: {$orders->getCountByStatus(OrderStatus::CANCELLED->value)}\n";

        return $report;
    }

    private function generateEmptyWeeklyReport(DateTimeImmutable $reportDate): string
    {
        $periodEnd = $reportDate->format('d.m.Y H:i');
        $periodStart = $reportDate->modify('-' . self::REPORT_PERIOD_HOURS . ' hours')->format('d.m.Y H:i');

        return "*Недельный отчет по заказам*\n"
            . "Период: {$periodStart} - {$periodEnd}\n\n"
            . "*Заказов за этот период не найдено*";
    }
}
