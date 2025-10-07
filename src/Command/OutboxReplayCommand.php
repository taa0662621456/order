<?php
declare(strict_types=1);

namespace OrderComponent\Command;

use OrderComponent\Service\Outbox\OutboxPublisher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'order:outbox:replay', description: 'Publish outbox messages via Messenger transport')]
final class OutboxReplayCommand extends Command
{
    public function __construct(private OutboxPublisher $publisher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Max messages per run', '100');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = (int)$input->getArgument('limit');
        $count = $this->publisher->replay($limit);
        $output->writeln(sprintf('<info>Published %d message(s) from outbox</info>', $count));
        return Command::SUCCESS;
    }
}
