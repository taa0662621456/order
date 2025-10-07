<?php
declare(strict_types=1);

namespace App\Console\Table;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class TableFactory
{
    public function create(OutputInterface $output, array $headers, array $rows): Table
    {
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($rows);
        return $table;
    }
}
