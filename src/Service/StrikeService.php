<?php

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class StrikeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security
    ) {}

    public function getStrikes(int $categoryId): array
    {
        $strikes = [];
        
        $user = $this->security->getUser();

        $today = new DateTimeImmutable('yesterday');
        $sql = 'SELECT CAST(r.created_at AS DATE) AS date_group, r.activity_id
                FROM records AS r
                INNER JOIN activities AS a ON a.id = r.activity_id
                INNER JOIN categories AS c ON c.id = a.category_id
                WHERE c.user_id = :userId 
                  AND r.created_at >= :dateFrom
                  AND r.created_at <= :dateTo
                  AND c.id = :catrgoryId
                GROUP BY r.activity_id, CAST(r.created_at AS DATE)
                HAVING COUNT(r.created_at) > 0 AND SUM(r.amount) >= MIN(a.goal)
                ORDER BY date_group DESC;
        ';

        $rawData = $this->entityManager->getConnection()->executeQuery($sql, [
            'dateFrom' => $today->modify('-1 year')->format('Y-m-d 00:00:00'),
            'dateTo' => $today->format('Y-m-d 23:59:59'),
            'catrgoryId' => $categoryId,
            'userId' => $user->getId(),
        ])->fetchAllAssociative();

        $yesterday = new DateTimeImmutable('yesterday');
        $previosData = $yesterday->format('Y-m-d');

        foreach ($rawData as $index => $date) {
            if ($previosData == $date['date_group']) {
                $strikes[$date['activity_id']] = 1;
                unset($rawData[$index]);
            }
        }

        foreach ($strikes as $activityId => $count) {
            $previosData = $yesterday->modify('-1 day')->format('Y-m-d');
            foreach ($rawData as $index => $date) {
                if ($activityId == $date['activity_id'] && $previosData == $date['date_group']) {
                    $strikes[$date['activity_id']] += 1;
                    unset($rawData[$index]);
                    $previosData = $yesterday->modify('-' . $strikes[$date['activity_id']] . ' day')->format('Y-m-d');
                }
            }
        }

        return $strikes;
    }
}