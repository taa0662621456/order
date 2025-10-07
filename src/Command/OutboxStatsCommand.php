<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\Monitoring\OutboxMonitor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:outbox:stats')]
final class OutboxStatsCommand extends Command
{
    public function __construct(private readonly OutboxMonitor $monitor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $stats = $this->monitor->stats();
        $output->writeln('Outbox pending: ' . $stats['pending']);
        $output->writeln('Outbox dispatched: ' . $stats['dispatched']);
        return Command::SUCCESS;
    }
}
