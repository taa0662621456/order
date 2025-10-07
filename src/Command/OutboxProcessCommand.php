<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use OrderComponent\Service\Outbox\OutboxProcessor;

#[AsCommand(name: 'order:outbox:process', description: 'Process outbox messages and dispatch domain events')]
final class OutboxProcessCommand extends Command
{
    public function __construct(private readonly OutboxProcessor $proc) { parent::__construct(); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $n = $this->proc->process();
        $io->success("Processed $n messages");
        return Command::SUCCESS;
    }
}
