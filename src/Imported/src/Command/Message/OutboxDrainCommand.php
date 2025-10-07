<?php
declare(strict_types=1);

namespace App\Command\Message;

use App\Command\AbstractDomainCommand;
use App\Command\Concerns\BatchCommandTrait;
use App\Command\Support\OutboxIdempotencyInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:message:outbox:drain', description: 'Drain outbox and publish domain events.')]
final class OutboxDrainCommand extends AbstractDomainCommand
{
    use BatchCommandTrait;

    public function __construct(private OutboxIdempotencyInterface $outbox) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addStandardOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->io($input, $output);
        [$ok, $release] = $this->acquireLock('outbox_drain');
        if (!$ok) { $io->warning('Another run in progress'); return self::SUCCESS; }

        $published = 0;
        $records = []; // TODO: iterable of outbox records

        foreach ($this->chunk($records, (int)$input->getOption('batch-size')) as $batch) {
            foreach ($batch as $rec) {
                $opId = 'outbox:' . (string)($rec->id ?? md5((string)$published));
                if ($this->outbox->alreadyProcessed($opId) || $this->isDryRun()) { continue; }
                // $this->bus->dispatch($rec->message);
                $this->outbox->markProcessed($opId);
                $published++;
            }
        }

        $io->successBox('Outbox drain', ['published: '.$published]);
        if ($release) { $release(); }
        return self::SUCCESS;
    }
}
