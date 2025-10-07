<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use OrderComponent\Service\Outbox\OutboxMessengerDispatcher;

#[AsCommand(name: 'order:outbox:replay', description: 'Replay pending outbox messages')]
final class OutboxReplayCommand extends Command
{
    public function __construct(private readonly OutboxMessengerDispatcher $disp) { parent::__construct(); }
    protected function configure(): void { $this->addArgument('limit', InputArgument::OPTIONAL, 'Max messages', 500); }
    protected function execute(InputInterface $input, OutputInterface $output): int
    { $io=new SymfonyStyle($input,$output); $n=$this->disp->dispatchPending((int)$input->getArgument('limit')); $io->success("Dispatched $n messages from outbox"); return Command::SUCCESS; }
}
