<?php
declare(strict_types=1);

namespace App\Service\Dashboard;

use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;

final class CustomerStatsService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function getNewCustomersToday(): int
    {
        $repo = $this->em->getRepository('App\\Entity\\User');
        $start = new DateTimeImmutable('today');
        // Try doctrine count by criteria-like
        try {
            if (method_exists($repo, 'createQueryBuilder')) {
                $qb = $repo->createQueryBuilder('u');
                return (int) $qb->select('COUNT(u.id)')
                    ->where('u.createdAt >= :start')->setParameter('start', $start)
                    ->getQuery()->getSingleScalarResult();
            }
        } catch (\Throwable) {}
        // Fallback
        $all = method_exists($repo, 'findAll') ? (array) $repo->findAll() : [];
        $n = 0;
        foreach ($all as $u) {
            if (method_exists($u, 'getCreatedAt') && $u->getCreatedAt() >= $start) {
                $n++;
            }
        }
        return $n;
    }

    public function getNewCustomersPerDay(int $days): array
    {
        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $day = (new DateTimeImmutable('today'))->modify("-{$i} day");
            $next = $day->modify('+1 day');
            $count = 0;
            try {
                $repo = $this->em->getRepository('App\\Entity\\User');
                if (method_exists($repo, 'createQueryBuilder')) {
                    $qb = $repo->createQueryBuilder('u');
                    $count = (int) $qb->select('COUNT(u.id)')
                        ->where('u.createdAt >= :from')->setParameter('from', $day)
                        ->andWhere('u.createdAt < :to')->setParameter('to', $next)
                        ->getQuery()->getSingleScalarResult();
                }
            } catch (\Throwable) {}
            $result[$day->format('Y-m-d')] = $count;
        }
        return array_reverse($result, true);
    }
}
