<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\Monitoring\RedisIdempotencyMonitor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:idempotency:stats')]
final class IdempotencyStatsCommand extends Command
{
    public function __construct(private readonly RedisIdempotencyMonitor $monitor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $stats = $this->monitor->stats();
        $output->writeln('Idempotency keys: ' . $stats['count']);
        $output->writeln('Sample TTLs:');
        foreach ($stats['sample_ttls'] as $k => $ttl) {
            $output->writeln("  $k => $ttl");
        }
        return Command::SUCCESS;
    }
}
