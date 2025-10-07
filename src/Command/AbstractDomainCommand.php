<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Command\Support\CommandStyle;
use App\Command\Concerns\WithStandardOptions;
use App\Command\Concerns\DryRunCommandTrait;
use App\Command\Concerns\LockingCommandTrait;

abstract class AbstractDomainCommand extends Command
{
    use WithStandardOptions;
    use DryRunCommandTrait;
    use LockingCommandTrait;

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->setDryRun((bool)$input->getOption('dry-run'));
    }

    protected function io(InputInterface $input, OutputInterface $output): CommandStyle
    {
        return CommandStyle::fromIO($input, $output);
    }
}
