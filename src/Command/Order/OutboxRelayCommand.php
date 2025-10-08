<?php
declare(strict_types=1);

namespace OrderComponent\Command\Order;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OrderComponent\Service\Order\OutboxRelay;

#[AsCommand(name: 'order:outbox:relay', description: 'Dispatch messages from outbox to Messenger')]
final class OutboxRelayCommand extends Command
{
    public function __construct(private readonly OutboxRelay $relay) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $n = $this->relay->runOnce(100);
        $output->writeln("<info>Relayed: {$n}</info>");
        return Command::SUCCESS;
    }
}
