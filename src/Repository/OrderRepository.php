<?php

namespace App\Repository;

use App\Collection\Order\OrderCollection;
use App\Entity\Order;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findOrdersByDateRange(DateTimeImmutable $startDate, DateTimeImmutable $endDate): OrderCollection
    {
        return new OrderCollection($this->createQueryBuilder('o')
            ->andWhere('o.orderDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult());
    }
}
