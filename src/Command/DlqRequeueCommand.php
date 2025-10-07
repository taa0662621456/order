<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Envelope;

#[AsCommand(name: 'order:dlq:requeue', description: 'Requeue messages from failure transport to async')]
final class DlqRequeueCommand extends Command
{
    public function __construct(private readonly ReceiverInterface $failed, private readonly SenderInterface $async) { parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = 0;
        foreach ($this->failed->get() as $envelope) {
            $this->async->send($envelope instanceof Envelope ? $envelope : new Envelope($envelope));
            $this->failed->ack($envelope);
            $count++;
        }
        $io->success("Requeued $count messages from failed → async");
        return Command::SUCCESS;
    }
}
