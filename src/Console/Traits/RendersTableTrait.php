<?php
declare(strict_types=1);

namespace App\Console\Traits;

use App\Console\Table\TableFactory;
use Symfony\Component\Console\Output\OutputInterface;

trait RendersTableTrait
{
    private TableFactory $tableFactory;

    public function setTableFactory(TableFactory $factory): void
    {
        $this->tableFactory = $factory;
    }

    protected function renderTable(array $headers, array $rows, OutputInterface $output): void
    {
        $table = $this->tableFactory->create($output, $headers, $rows);
        $table->render();
    }
}
