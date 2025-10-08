<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use OrderComponent\Service\Outbox\OutboxMessengerDispatcher;

#[AsCommand(name: 'order:outbox:dispatch', description: 'Send pending outbox events to Messenger transport')]
final class OutboxDispatchCommand extends Command
{
    public function __construct(private readonly OutboxMessengerDispatcher $disp) { parent::__construct(); }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @throws \JsonException
     * @throws \Symfony\Component\Messenger\Exception\ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $n = $this->disp->dispatchPending();
        $io->success("Dispatched $n messages");
        return Command::SUCCESS;
    }
}
