<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

final class CommandRunner extends Command
{
    protected static $defaultName = 'app:command-runner';

    public function __construct(private Application $application)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Run another Symfony console command by name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // This command acts as a utility; no default action.
        return self::SUCCESS;
    }

    /**
     * @param array<string,mixed> $parameters
     */
    public function runCommand(string $name, array $parameters, OutputInterface $output): int
    {
        $command = $this->application->find($name);
        $input = new ArrayInput($parameters + ['command' => $name]);
        return $command->run($input, $output);
    }

    protected function createProgressBar(OutputInterface $output, int $length = 10): ProgressBar
    {
        $progress = new ProgressBar($output);
        $progress->setBarCharacter('<info>░</info>');
        $progress->setEmptyBarCharacter(' ');
        $progress->setProgressCharacter('<comment>░</comment>');
        $progress->start($length);
        return $progress;
    }
}
