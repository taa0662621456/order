<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\OutputInterface;

final readonly class CommandExecutor
{
    public function __construct(private Application $application) {}

    /**
     * Run a Symfony console command by name with given parameters.
     * @param string $name
     * @param array $parameters
     * @param OutputInterface $output
     * @return int
     * @throws ExceptionInterface
     */
    public function runCommand(string $name, array $parameters, OutputInterface $output): int
    {
        $command = $this->application->find($name);
        $input = new \Symfony\Component\Console\Input\ArrayInput($parameters + ['command' => $name]);
        try {
            return $command->run($input, $output);
        } catch (ExceptionInterface $e) {
        }
        return 0;
    }
}
