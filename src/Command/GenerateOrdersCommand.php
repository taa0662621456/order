<?php
declare(strict_types=1);
namespace OrderComponent\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zenstruck\Foundry\Proxy;
use OrderComponent\Factory\OrderFactory;

#[AsCommand(name: 'order:generate', description: 'Generate N orders via Foundry')]
final class GenerateOrdersCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::OPTIONAL, 'How many orders to generate', 5);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $count = (int)$input->getArgument('count');
        if ($count <= 0) { $io->error('Count must be > 0'); return Command::FAILURE; }

        /** @var Proxy[] $orders */
        $orders = OrderFactory::createMany($count);
        $io->success(sprintf('Created %d orders (Создано %d заказов)', count($orders), count($orders)));
        return Command::SUCCESS;
    }
}
