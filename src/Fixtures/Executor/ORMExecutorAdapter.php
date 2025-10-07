<?php
declare(strict_types=1);

namespace App\Fixtures\Executor;

use Doctrine\ORM\EntityManagerInterface;

final class ORMExecutorAdapter
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @param array<int,object> $fixtures
     */
    public function execute(array $fixtures, bool $append): void
    {
        if (class_exists(\Doctrine\Common\DataFixtures\Executor\ORMExecutor::class)
            && class_exists(\Doctrine\Common\DataFixtures\Purger\ORMPurger::class)) {
            $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
            $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
            $executor->execute($fixtures, $append);
            return;
        }

        foreach ($fixtures as $fixture) {
            if (method_exists($fixture, 'load')) {
                $fixture->load($this->em);
            }
        }
    }
}
