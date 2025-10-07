<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use OrderComponent\Service\Outbox\OutboxProcessor;
use OrderComponent\Entity\Outbox\OutboxMessage;

#[AsCommand(name: 'outbox:replay', description: 'Replay outbox messages')]
final class OutboxReplayCommand extends Command
{
    public function __construct(private readonly OutboxProcessor $proc){ parent::__construct(); }

    protected function configure(): void
    {
        $this->addArgument('batch', InputArgument::OPTIONAL, 'Batch size', 50);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $batch = (int)$input->getArgument('batch');

        // Example mappers/dispatchers (no real bus here)
        $processed = $this->proc->replay(
            $batch,
            fn(OutboxMessage $m) => (object)['name' => $m->getEventName(), 'payload' => $m->getPayload()],
            fn(object $e) => null
        );
        $io->success(sprintf('Replayed %d messages', $processed));
        return Command::SUCCESS;
    }
}
