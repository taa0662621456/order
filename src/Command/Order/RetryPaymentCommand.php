<?php
declare(strict_types=1);

namespace App\Command\Order;

use App\Command\AbstractDomainCommand;
use App\Command\Concerns\BatchCommandTrait;
use App\Interface\Order\OrderPaymentServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:order:retry-payment', description: 'Retry payments for orders with failed payment status since --since.')]
final class RetryPaymentCommand extends AbstractDomainCommand
{
    use BatchCommandTrait;

    public function __construct(private OrderPaymentServiceInterface $payments) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addStandardOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->io($input, $output);
        [$ok, $release] = $this->acquireLock('order_retry_payment');
        if (!$ok) { $io->warning('Another run in progress'); return self::SUCCESS; }

        $since = $input->getOption('since') ? new \DateTimeImmutable((string)$input->getOption('since')) : (new \DateTimeImmutable('-1 day'));
        $retried = 0;

        foreach ($this->chunk($this->payments->failedSince($since), (int)$input->getOption('batch-size')) as $batch) {
            foreach ($batch as $orderId) {
                if ($this->isDryRun()) { continue; }
                $this->payments->retryPayment($orderId);
                $retried++;
            }
        }

        $io->successBox('Order payment retry', ['since: '.$since->format(DATE_ATOM), 'retried: '.$retried]);
        if ($release) { $release(); }
        return self::SUCCESS;
    }
}
