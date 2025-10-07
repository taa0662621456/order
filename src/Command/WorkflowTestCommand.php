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
use OrderComponent\Service\Order\OrderWorkflowService;

#[AsCommand(name: 'order:workflow:test', description: 'Simulate order workflow and event dispatching')]
final class WorkflowTestCommand extends Command
{
    public function __construct(private readonly OrderWorkflowService $svc){ parent::__construct(); }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $order = new Order();
        $this->svc->place($order);
        $this->svc->pay($order);
        $this->svc->ship($order);
        $io->success('Workflow simulated: draft→placed→paid→shipped');
        return Command::SUCCESS;
    }
}
