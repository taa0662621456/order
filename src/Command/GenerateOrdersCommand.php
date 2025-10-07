<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Proxy;
use OrderComponent\Factory\OrderFactory;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderPayment;
use OrderComponent\ValueObject\Order\OrderStatus;

#[AsCommand(name: 'order:generate', description: 'Generate N orders via Foundry (optional payments)')]
final class GenerateOrdersCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em){ parent::__construct(); }

    protected function configure(): void
    {
        $this
            ->addArgument('count', InputArgument::OPTIONAL, 'How many orders to generate', 5)
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'Force status for all orders')
            ->addOption('seed', null, InputOption::VALUE_REQUIRED, 'Seed for RNG')
            ->addOption('with-payment', null, InputOption::VALUE_NONE, 'Create OrderPayment for each order (amount = 1000)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = (int)$input->getArgument('count');
        if ($count <= 0) { $io->error('Count must be > 0'); return Command::FAILURE; }

        if ($seed = $input->getOption('seed')) { mt_srand((int)$seed); }

        $statusOpt = $input->getOption('status');
        $forcedStatus = null;
        if ($statusOpt) {
            $statusOpt = strtolower((string)$statusOpt);
            $map = [
                'draft'=>OrderStatus::Draft,'placed'=>OrderStatus::Placed,'paid'=>OrderStatus::Paid,
                'shipped'=>OrderStatus::Shipped,'completed'=>OrderStatus::Completed,
                'cancelled'=>OrderStatus::Cancelled,'refunded'=>OrderStatus::Refunded
            ];
            if (!isset($map[$statusOpt])) {
                $io->error('Unknown status: '.$statusOpt);
                return Command::FAILURE;
            }
            $forcedStatus = $map[$statusOpt];
        }

        /** @var Proxy[] $proxies */
        $proxies = OrderFactory::createMany($count);
        $ids = [];
        $paymentTotal = 0;
        foreach ($proxies as $proxy) {
            /** @var Order $order */
            $order = $proxy->object();
            if ($forcedStatus) $order->setStatus($forcedStatus);
            $this->em->persist($order);
            $this->em->flush();
            $ids[] = $order->getId();

            if ($input->getOption('with-payment')) {
                $p = new OrderPayment();
                $p->setOrder($order);
                $p->setAmount(1000);
                $this->em->persist($p);
                $paymentTotal += 1000;
            }
        }
        $this->em->flush();

        $withPayments = $input->getOption('with-payment');
        $io->success(sprintf(
            $withPayments ? 'Created %d orders with payments (Создано %d заказов с платежами)' : 'Created %d orders (Создано %d заказов)',
            count($ids), count($ids)
        ));
        if ($forcedStatus) {
            $io->writeln(sprintf('Status: %s', $forcedStatus->value));
        }
        $io->writeln('Payment amount: $1000');
        if ($withPayments) {
            $io->writeln(sprintf('Payment total: $%d (Общий платёж: $%d)', $paymentTotal, $paymentTotal));
        }
        $io->writeln('IDs: ['.implode(', ', $ids).']');
        return Command::SUCCESS;
    }
}
