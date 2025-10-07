<?php
declare(strict_types=1);

namespace App\Command\Order;

use App\Command\AbstractDomainCommand;
use App\Command\Concerns\BatchCommandTrait;
use App\Command\Concerns\TransactionalCommandTrait;
use App\Interface\Order\OrderRepositoryInterface;
use App\Interface\Order\OrderServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:order:close-stale', description: 'Close orders stuck in pending/payment_failed states older than --since')]
final class CloseStaleOrdersCommand extends AbstractDomainCommand
{
    use BatchCommandTrait;
    use TransactionalCommandTrait;

    public function __construct(
        private OrderRepositoryInterface $orders,
        private OrderServiceInterface $service,
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addStandardOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->io($input, $output);
        [$acq, $release] = $this->acquireLock('order_close_stale');
        if (!$acq) { $io->warning('Another instance is running.'); return self::SUCCESS; }

        $since = $input->getOption('since') ? new \DateTimeImmutable((string)$input->getOption('since')) : (new \DateTimeImmutable('-30 days'));
        $limit = (int)$input->getOption('limit');
        $offset = (int)$input->getOption('offset');

        $processed = 0; $closed = 0; $skipped = 0; $errors = 0;
        $iter = $this->orders->findStale($since, $limit, $offset);

        foreach ($this->chunk($iter, (int)$input->getOption('batch-size')) as $batch) {
            $this->transactional($this->em, function() use ($batch, &$processed, &$closed, &$skipped, &$errors) {
                foreach ($batch as $order) {
                    $processed++;
                    try {
                        if ($this->isDryRun()) { $skipped++; continue; }
                        $this->service->closeAsExpired($order);
                        $closed++;
                    } catch (\Throwable $e) {
                        $errors++;
                    }
                }
            });
            // можно добавить $this->em->clear() если нужно
        }

        $io->successBox('Done', [
            'processed: '.$processed,
            'closed: '.$closed,
            'skipped: '.$skipped,
            'errors: '.$errors,
        ]);

        if ($release) { $release(); }
        return self::SUCCESS;
    }
}
