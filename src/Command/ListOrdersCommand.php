<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use OrderComponent\Entity\Order;
use OrderComponent\Entity\Order\OrderPayment;

#[AsCommand(name: 'order:list', description: 'List orders (and payments)')]
final class ListOrdersCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em){ parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $orders = $this->em->getRepository(Order::class)->findAll();
        $payments = $this->em->getRepository(OrderPayment::class)->findAll();
        $io->writeln(sprintf('Orders: %d', count($orders)));
        foreach ($orders as $o) {
            $io->writeln(sprintf('- Order #%d status=%s', $o->getId(), $o->getStatus()->value));
        }
        $total = 0;
        foreach ($payments as $p) $total += $p->getAmount();
        $io->writeln(sprintf('Payments: %d, Total: $%d', count($payments), $total));
        return Command::SUCCESS;
    }
}
